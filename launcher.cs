using System;
using System.Diagnostics;
using System.Drawing;
using System.IO;
using System.Net.Sockets;
using System.Text;
using System.Threading;
using System.Windows.Forms;
using Microsoft.Web.WebView2.Core;
using Microsoft.Web.WebView2.WinForms;
using Microsoft.Win32;

class Program
{
    const string Host = "127.0.0.1";
    const int Port = 8181;

    const int MaxAttempts = 3;

    [STAThread]
    static void Main()
    {
        try
        {
            if (!Environment.Is64BitOperatingSystem)
            {
                MessageBox.Show(
                    "Este equipo tiene Windows de 32 bits, pero el PHP incluido con el sistema requiere Windows de 64 bits.\n" +
                    "El sistema no puede funcionar en este equipo.",
                    "Limpiaplus 360 - Equipo no compatible",
                    MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            KillExistingPhp();
            RunMigrations();

            Process phpProcess = null;
            bool ready = false;

            for (int attempt = 1; attempt <= MaxAttempts && !ready; attempt++)
            {
                phpProcess = StartPhp();

                // Esperar activamente a que PHP acepte conexiones en vez de un tiempo fijo:
                // en una instalación nueva, el antivirus suele escanear en tiempo real el
                // php.exe recién extraído (no firmado) y eso puede tardar más de 1-2 segundos,
                // dejando el navegador en blanco si se abre demasiado pronto.
                ready = WaitForServer(phpProcess, TimeSpan.FromSeconds(25));

                if (!ready)
                {
                    // Probablemente el puerto seguía ocupado por una instancia anterior que
                    // no había soltado el socket todavía: matar todo de nuevo, dar tiempo a
                    // que el sistema operativo libere el puerto, y reintentar.
                    KillExistingPhp();
                    Thread.Sleep(1000);
                }
            }

            if (!ready)
            {
                int exitCode = (phpProcess != null && phpProcess.HasExited) ? phpProcess.ExitCode : -1;
                MessageBox.Show(
                    "El servidor no pudo iniciarse tras " + MaxAttempts + " intentos (código de salida " + exitCode + ").\n" +
                    "Es posible que el antivirus esté bloqueando " + @"php\php.exe" + " o que el puerto " + Port + " esté en uso por otro programa.\n\n" +
                    "Revisa " + @"storage\logs\php-server.log" + " para más detalles.",
                    "Limpiaplus 360 - Error al iniciar",
                    MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            RunAppWindow(phpProcess);
        }
        catch (Exception ex)
        {
            // Mostrar SIEMPRE algo visible: si esto queda en un Console.WriteLine (sin
            // consola porque el programa es winexe), el fallo es completamente invisible
            // para el usuario - ni pantalla en blanco, ni error, ni navegador.
            MessageBox.Show(
                "No se pudo iniciar el sistema.\n\n" +
                "Tipo: " + ex.GetType().Name + "\n" +
                "Detalle: " + ex.Message,
                "Limpiaplus 360 - Error al iniciar",
                MessageBoxButtons.OK, MessageBoxIcon.Error);
        }
    }

    // Mata instancias previas del servidor PHP y espera a que realmente terminen
    // (Kill() por sí solo no garantiza que el puerto quede libre de inmediato).
    static void KillExistingPhp()
    {
        foreach (var process in Process.GetProcessesByName("php"))
        {
            try
            {
                process.Kill();
                process.WaitForExit(3000);
            }
            catch { }
        }
    }

    // Antes esto lanzaba msedge.exe como proceso APARTE (--app=...): un proceso
    // de navegador de verdad, con todo lo que eso implica - SmartScreen podía
    // interceptarlo, Edge podía reenviar los argumentos a una instancia ya
    // corriendo en vez de abrir ventana nueva, y launcher.exe no tenía forma
    // de saber si esa ventana seguía abierta o no (se abría el navegador y el
    // launcher se cerraba de inmediato, dejando php.exe corriendo huérfano
    // para siempre en segundo plano).
    //
    // Ahora la propia ventana del launcher aloja el motor de Edge embebido
    // (WebView2): no hay proceso de navegador aparte, la ventana ES la app, y
    // al cerrarla se apaga php.exe también. WebView2 Runtime viene preinstalado
    // en Windows 10/11 actualizados (es el mismo motor que usa Edge), así que
    // en la inmensa mayoría de los equipos ya está disponible.
    static void RunAppWindow(Process phpProcess)
    {
        string url = "http://" + Host + ":" + Port;

        if (CoreWebView2Environment.GetAvailableBrowserVersionString() == null)
        {
            // Caso raro: equipo sin el WebView2 Runtime instalado. En vez de
            // dejar la app sin abrir, usar el viejo método (Edge aparte, o el
            // navegador predeterminado) como último recurso.
            OpenInExternalBrowser(url);
            return;
        }

        Application.EnableVisualStyles();
        Application.SetCompatibleTextRenderingDefault(false);

        using (var form = new AppWindow(url))
        {
            Application.Run(form);
        }

        try
        {
            if (!phpProcess.HasExited) phpProcess.Kill();
        }
        catch { }
    }

    // Respaldo para cuando WebView2 Runtime no está disponible: abre Edge en
    // modo "app" (sin barra de direcciones) con un --user-data-dir propio para
    // evitar que Chromium reenvíe los argumentos a una instancia ya corriendo,
    // o si Edge no aparece, el navegador predeterminado del sistema.
    static void OpenInExternalBrowser(string url)
    {
        string edgePath = ResolveMsEdgePath();

        if (edgePath == null)
        {
            Process.Start(new ProcessStartInfo
            {
                FileName = url,
                UseShellExecute = true
            });
            return;
        }

        string profileDir = Path.Combine(
            Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData),
            "Limpiaplus360", "EdgeAppData");
        Directory.CreateDirectory(profileDir);

        Process.Start(new ProcessStartInfo
        {
            FileName = edgePath,
            Arguments = "--app=" + url +
                " --user-data-dir=\"" + profileDir + "\"" +
                " --no-first-run --disable-gpu",
            UseShellExecute = false,
            CreateNoWindow = true
        });
    }

    // Resuelve la ruta real de msedge.exe leyendo el mismo registro de App
    // Paths que usa "start msedge", con un par de rutas fijas como respaldo.
    static string ResolveMsEdgePath()
    {
        try
        {
            using (var key = Registry.LocalMachine.OpenSubKey(
                @"SOFTWARE\Microsoft\Windows\CurrentVersion\App Paths\msedge.exe"))
            {
                var path = key != null ? key.GetValue(null) as string : null;
                if (!string.IsNullOrEmpty(path) && File.Exists(path))
                {
                    return path;
                }
            }
        }
        catch { }

        string[] fallbacks =
        {
            Environment.ExpandEnvironmentVariables(@"%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe"),
            Environment.ExpandEnvironmentVariables(@"%ProgramFiles%\Microsoft\Edge\Application\msedge.exe"),
            Environment.ExpandEnvironmentVariables(@"%LocalAppData%\Microsoft\Edge\Application\msedge.exe"),
        };

        foreach (var candidate in fallbacks)
        {
            if (File.Exists(candidate))
            {
                return candidate;
            }
        }

        return null;
    }

    // Reinstalar/actualizar reemplaza el CÓDIGO (via [InstallDelete] + [Files] en el
    // instalador), pero nunca toca database.sqlite (se preserva a propósito, es la
    // información real del negocio). Si una versión nueva agrega una migración, esa
    // base de datos vieja se queda desactualizada para siempre a menos que algo corra
    // "artisan migrate" - y nada lo hacía. Se detectó así: en un equipo con una base de
    // datos de junio, un error de julio (columna NOT NULL que ya tiene migración desde
    // el 18 de agosto) seguía sin resolverse en agosto.
    //
    // "migrate" es seguro de correr en cada apertura: si no hay nada pendiente, Laravel
    // solo consulta la tabla migrations y termina casi al instante. Se ejecuta ANTES de
    // levantar el servidor para que ninguna petición llegue a golpear un esquema viejo.
    static void RunMigrations()
    {
        try
        {
            Process p = new Process();
            p.StartInfo.FileName = @"php\php.exe";
            p.StartInfo.Arguments = "artisan migrate --force --no-interaction";
            p.StartInfo.UseShellExecute = false;
            p.StartInfo.CreateNoWindow = true;
            p.StartInfo.RedirectStandardOutput = true;
            p.StartInfo.RedirectStandardError = true;

            var output = new StringBuilder();
            DataReceivedEventHandler onData = (sender, e) =>
            {
                if (e.Data != null) lock (output) { output.AppendLine(e.Data); }
            };
            p.OutputDataReceived += onData;
            p.ErrorDataReceived += onData;

            p.Start();
            p.BeginOutputReadLine();
            p.BeginErrorReadLine();

            if (!p.WaitForExit(30000))
            {
                try { p.Kill(); } catch { }
                lock (output) { output.AppendLine("[launcher] Tiempo de espera agotado (30s) ejecutando migrate."); }
            }

            Directory.CreateDirectory(@"storage\logs");
            lock (output) { File.WriteAllText(@"storage\logs\migrate.log", output.ToString()); }
        }
        catch
        {
            // Si algo falla al intentar migrar (php.exe no encontrado, etc.), seguir
            // arrancando el servidor igual: preferible una app con esquema desactualizado
            // a que no arranque nunca por esto.
        }
    }

    // Antes, la salida de php.exe se descartaba por completo (CreateNoWindow + sin
    // redirección): si el fallo era un error fatal de PHP (no una excepción que Laravel
    // pudiera capturar y anotar en storage/logs/laravel.log) quedaba completamente
    // invisible - ni en pantalla, ni en ningún log - y el síntoma era una pantalla en
    // blanco sin ninguna pista de qué pasó. Ahora se redirige todo a un archivo para
    // poder diagnosticar ese tipo de fallos.
    const string PhpLogPath = @"storage\logs\php-server.log";

    static Process StartPhp()
    {
        Process phpProcess = new Process();
        phpProcess.StartInfo.FileName = @"php\php.exe";
        phpProcess.StartInfo.Arguments = "-S " + Host + ":" + Port + " -t public server.php";
        phpProcess.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;
        phpProcess.StartInfo.CreateNoWindow = true;
        phpProcess.StartInfo.UseShellExecute = false;
        phpProcess.StartInfo.RedirectStandardOutput = true;
        phpProcess.StartInfo.RedirectStandardError = true;

        StreamWriter logWriter = null;
        try
        {
            Directory.CreateDirectory(Path.GetDirectoryName(PhpLogPath));
            logWriter = new StreamWriter(PhpLogPath, append: false);
            logWriter.AutoFlush = true;
        }
        catch
        {
            // Si no se puede abrir el log (permisos, disco lleno, etc.), seguir sin él:
            // preferible perder el diagnóstico a impedir que el servidor arranque.
        }

        if (logWriter != null)
        {
            DataReceivedEventHandler onData = (sender, e) =>
            {
                if (e.Data == null) return;
                try { logWriter.WriteLine(e.Data); } catch { }
            };
            phpProcess.OutputDataReceived += onData;
            phpProcess.ErrorDataReceived += onData;
        }

        phpProcess.Start();

        if (logWriter != null)
        {
            phpProcess.BeginOutputReadLine();
            phpProcess.BeginErrorReadLine();
        }

        return phpProcess;
    }

    // Sondea el puerto del servidor embebido hasta que responda como un servidor HTTP,
    // el proceso muera, o se agote el tiempo máximo de espera.
    //
    // No basta con comprobar que el puerto acepta conexiones TCP: si en ese equipo
    // específico ya hay OTRO programa escuchando en el puerto 8181 (que no es "php.exe" y
    // por tanto KillExistingPhp no lo toca), un simple connect() exitoso haría creer que
    // el servidor está listo y abriría el navegador contra ese otro programa - típicamente
    // una página en blanco, sin ningún síntoma de error. Por eso se envía un GET real y se
    // exige una respuesta que empiece por "HTTP/", para confirmar que quien contesta es
    // realmente un servidor HTTP (nuestro php.exe) y no otra cosa.
    static bool WaitForServer(Process phpProcess, TimeSpan timeout)
    {
        var deadline = DateTime.UtcNow + timeout;
        while (DateTime.UtcNow < deadline)
        {
            if (phpProcess.HasExited)
            {
                return false;
            }

            if (LooksLikeHttpServer())
            {
                return true;
            }

            Thread.Sleep(300);
        }

        return !phpProcess.HasExited && LooksLikeHttpServer();
    }

    static bool LooksLikeHttpServer()
    {
        try
        {
            using (var client = new TcpClient())
            {
                var result = client.BeginConnect(Host, Port, null, null);
                if (!result.AsyncWaitHandle.WaitOne(500) || !client.Connected)
                {
                    return false;
                }
                client.EndConnect(result);

                using (var stream = client.GetStream())
                {
                    stream.WriteTimeout = 1000;
                    stream.ReadTimeout = 1000;

                    byte[] request = Encoding.ASCII.GetBytes(
                        "GET / HTTP/1.0\r\nHost: " + Host + "\r\nConnection: close\r\n\r\n");
                    stream.Write(request, 0, request.Length);

                    byte[] buffer = new byte[5];
                    int read = stream.Read(buffer, 0, buffer.Length);
                    return read == buffer.Length &&
                        Encoding.ASCII.GetString(buffer) == "HTTP/";
                }
            }
        }
        catch
        {
            return false;
        }
    }
}

// Ventana nativa que aloja el motor de Edge embebido (WebView2) apuntando al
// servidor PHP local. Reemplaza a "abrir Edge como app aparte": esta ventana
// ES la aplicación, no un navegador comunicándose con un proceso externo.
class AppWindow : Form
{
    readonly string url;
    readonly WebView2 webView;

    public AppWindow(string url)
    {
        this.url = url;

        Text = "Limpiaplus 360 - Panel de Administración";
        Width = 1280;
        Height = 800;
        StartPosition = FormStartPosition.CenterScreen;
        WindowState = FormWindowState.Maximized;

        try
        {
            string iconPath = Path.Combine("public", "favicon.ico");
            if (File.Exists(iconPath))
            {
                Icon = new Icon(iconPath);
            }
        }
        catch { }

        webView = new WebView2();
        webView.Dock = DockStyle.Fill;
        Controls.Add(webView);

        Load += AppWindow_Load;
    }

    async void AppWindow_Load(object sender, EventArgs e)
    {
        try
        {
            // Carpeta de datos propia y escribible por cualquier usuario (no la
            // carpeta de instalación, que puede no ser escribible para un
            // usuario sin privilegios de administrador): ahí WebView2 guarda su
            // caché, cookies y sesión entre aperturas de la app.
            string userDataFolder = Path.Combine(
                Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData),
                "Limpiaplus360", "WebView2Data");
            Directory.CreateDirectory(userDataFolder);

            var env = await CoreWebView2Environment.CreateAsync(null, userDataFolder);
            await webView.EnsureCoreWebView2Async(env);
            webView.CoreWebView2.Navigate(url);
        }
        catch (Exception ex)
        {
            MessageBox.Show(
                "No se pudo iniciar la ventana de la aplicación (WebView2).\n\n" +
                "Detalle: " + ex.Message,
                "Limpiaplus 360 - Error al iniciar",
                MessageBoxButtons.OK, MessageBoxIcon.Error);
            Close();
        }
    }
}

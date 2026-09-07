#define MyAppName "Limpiaplus 360"
#define MyAppVersion "1.0.0"
#define MyAppPublisher "Limpiaplus 360"
#define MyAppExeName "launcher.exe"

[Setup]
; GUID fijo: no cambiar entre versiones, permite que Windows reconozca
; reinstalaciones/actualizaciones como la MISMA aplicación (no una nueva).
AppId={{B4E1C9A2-7D3F-4A6E-9C1B-2F8E5D6A9C31}
AppName={#MyAppName}
AppVersion={#MyAppVersion}
AppPublisher={#MyAppPublisher}
AppSupportURL=https://Inventario.test
DefaultDirName=C:\Inventario
DisableDirPage=yes
DefaultGroupName={#MyAppName}
DisableProgramGroupPage=yes
UninstallDisplayIcon={app}\public\favicon.ico
UninstallDisplayName={#MyAppName}
OutputDir=.
OutputBaseFilename=Instalar_Inventario
SetupIconFile=public\favicon.ico
Compression=lzma2
SolidCompression=yes
PrivilegesRequired=admin
WizardStyle=modern
ArchitecturesInstallIn64BitMode=x64compatible
DisableWelcomePage=no
; Detecta con Restart Manager de Windows cualquier proceso que tenga abiertos
; archivos de {app} (además del taskkill explícito de PrepareToInstall) y
; permite cerrarlo automáticamente en vez de fallar en silencio por archivo bloqueado.
CloseApplications=yes
CloseApplicationsFilter=*.exe
RestartApplications=no

[Languages]
Name: "spanish"; MessagesFile: "compiler:Languages\Spanish.isl"

[Files]
; Todo el código y el runtime de PHP, excluyendo herramientas de desarrollo,
; datos del usuario y artefactos temporales/de caché que no deben empaquetarse.
Source: "*"; DestDir: "{app}"; Flags: ignoreversion recursesubdirs createallsubdirs; Excludes: ".claude,.claude\*,.idea,.idea\*,.vscode,.vscode\*,.git,.git\*,Exel,Exel\*,node_modules,node_modules\*,tests,tests\*,.editorconfig,.gitattributes,.gitignore,.phpunit.result.cache,CrearAccesoDirecto.bat,Instalar.bat,Instalar_Inventario.exe,README.md,empaquetar.bat,launcher.cs,package-lock.json,package.json,phpunit.xml,sfx_config.txt,vite.config.js,installer.iss,.env,database\database.sqlite,storage\app\*.zip,storage\framework\cache\data\*,storage\framework\cache\laravel-excel\*,storage\framework\sessions\*,storage\framework\testing\*,storage\framework\views\*,storage\logs\*.log,public\img\logo.png"

; Datos propios del negocio: solo se copian si NO existen ya (instalación nueva),
; y el desinstalador nunca los borra (para no perder información al actualizar
; o desinstalar la aplicación).
Source: ".env"; DestDir: "{app}"; Flags: onlyifdoesntexist uninsneveruninstall
Source: "database\database.sqlite"; DestDir: "{app}\database"; Flags: onlyifdoesntexist uninsneveruninstall
Source: "public\img\logo.png"; DestDir: "{app}\public\img"; Flags: onlyifdoesntexist uninsneveruninstall

[InstallDelete]
; Inno Setup, por defecto, solo AGREGA o SOBRESCRIBE los archivos que vienen en
; el paquete actual - nunca borra lo que sobra de una versión anterior. Con
; código que va cambiando de una versión a otra (una vista renombrada, un
; controlador eliminado, una dependencia de vendor/ que ya no se usa) eso deja
; basura huérfana en cada actualización. El caso más notorio es
; public\build: Vite nombra sus archivos con un hash distinto en cada
; compilación, así que los .js/.css de la build anterior NUNCA se sobrescriben,
; solo se acumulan para siempre.
;
; Por eso, antes de copiar los archivos de la nueva versión, se borran por
; completo las carpetas de CÓDIGO/runtime de la versión anterior. Los datos del
; negocio (.env, database\database.sqlite, public\img\logo.png, storage\) NO
; están en esta lista - esos se preservan siempre, en cualquier actualización.
Type: filesandordirs; Name: "{app}\app"
Type: filesandordirs; Name: "{app}\config"
Type: filesandordirs; Name: "{app}\database\factories"
Type: filesandordirs; Name: "{app}\database\migrations"
Type: filesandordirs; Name: "{app}\database\seeders"
Type: filesandordirs; Name: "{app}\lang"
Type: filesandordirs; Name: "{app}\php"
Type: filesandordirs; Name: "{app}\public\build"
Type: filesandordirs; Name: "{app}\public\css"
Type: filesandordirs; Name: "{app}\public\images"
Type: filesandordirs; Name: "{app}\public\vendor"
Type: filesandordirs; Name: "{app}\resources"
Type: filesandordirs; Name: "{app}\routes"
Type: filesandordirs; Name: "{app}\vendor"
Type: files; Name: "{app}\artisan"
Type: files; Name: "{app}\composer.json"
Type: files; Name: "{app}\composer.lock"
Type: files; Name: "{app}\server.php"

[Dirs]
; Estas carpetas quedan con TODO su contenido excluido en [Files] (para no
; llevar cache/sesiones de desarrollo), y Inno Setup NO crea carpetas que no
; reciben ningun archivo. Sin esto, en una instalacion nueva (equipo sin
; instalacion previa) estas carpetas no existen en absoluto, Laravel no puede
; escribir sus vistas compiladas/sesiones/cache al primer request, y como
; ademas falla al intentar renderizar su propia pagina de error, el resultado
; es una pantalla completamente en blanco en vez de un mensaje de error.
; Permissions: users-modify asegura que un usuario NO administrador (quien de
; verdad usa el programa a diario) pueda escribir ahi, no solo el admin que
; corrio el instalador.
Name: "{app}\storage\framework\sessions"; Permissions: users-modify
Name: "{app}\storage\framework\views"; Permissions: users-modify
Name: "{app}\storage\framework\cache\data"; Permissions: users-modify
Name: "{app}\storage\framework\cache\laravel-excel"; Permissions: users-modify
Name: "{app}\storage\framework\testing"; Permissions: users-modify
Name: "{app}\storage\logs"; Permissions: users-modify
Name: "{app}\storage\app\public"; Permissions: users-modify
Name: "{app}\storage\app\private"; Permissions: users-modify
Name: "{app}\bootstrap\cache"; Permissions: users-modify
; database.sqlite se copia via [Files] arriba, pero SQLite tambien necesita
; poder crear ahi sus archivos temporales -journal/-wal: hace falta permiso
; de escritura sobre la CARPETA, no solo sobre el archivo .sqlite.
Name: "{app}\database"; Permissions: users-modify

[Icons]
Name: "{group}\{#MyAppName}"; Filename: "{app}\{#MyAppExeName}"; WorkingDir: "{app}"; IconFilename: "{app}\public\favicon.ico"
Name: "{group}\Desinstalar {#MyAppName}"; Filename: "{uninstallexe}"
Name: "{autodesktop}\{#MyAppName}"; Filename: "{app}\{#MyAppExeName}"; WorkingDir: "{app}"; IconFilename: "{app}\public\favicon.ico"

[Run]
Filename: "{app}\{#MyAppExeName}"; Description: "Abrir {#MyAppName}"; Flags: nowait postinstall skipifsilent

[UninstallRun]
; Cerrar el servidor PHP embebido antes de desinstalar, si sigue corriendo.
Filename: "{cmd}"; Parameters: "/c taskkill /F /IM php.exe /T"; Flags: runhidden skipifdoesntexist waituntilterminated; RunOnceId: "KillPhp"

[Code]
// Si ya hay una instalación previa corriendo (php.exe / launcher.exe), cerrarla
// antes de copiar archivos. Sin esto, una reinstalación sobre una instalación
// en uso falla porque Windows no permite sobrescribir los .exe/.dll bloqueados:
// Inno muestra un diálogo de "archivo en uso" por CADA archivo bloqueado, y si
// se cierra sin leerlo (Ignorar), esos archivos quedan SIN copiar aunque al
// final diga "Instalación completada" - así se detectó este bug: la app
// quedaba con launcher.exe y php.exe faltantes tras una reinstalación limpia.
//
// Ruta completa a taskkill.exe (no confiar en que "taskkill.exe" se resuelva
// por PATH bajo el proceso elevado de Setup) + Sleep para dar tiempo real a
// que Windows libere los archivos antes de que Inno intente sobrescribirlos.
function PrepareToInstall(var NeedsRestart: Boolean): String;
var
  ResultCode: Integer;
  TaskKillPath: String;
begin
  TaskKillPath := ExpandConstant('{sys}\taskkill.exe');
  Exec(TaskKillPath, '/F /IM php.exe /T', '', SW_HIDE, ewWaitUntilTerminated, ResultCode);
  Exec(TaskKillPath, '/F /IM launcher.exe /T', '', SW_HIDE, ewWaitUntilTerminated, ResultCode);
  Sleep(2000);
  Result := '';
end;

// En al menos un equipo, php.exe moría instantes después de arrancar con el
// código de salida 525 (Win32 ERROR_NOT_SUPPORTED_IN_TRANSACTION: "la operación
// especificada no se admite en un archivo de sistema") SIN imprimir ningún
// mensaje propio y sin dejar nada en storage/logs - es decir, algo externo lo
// mataba, no un error de PHP. Eso apunta a Windows Defender: "Acceso controlado
// a carpetas" (protección anti-ransomware) bloquea en silencio que ejecutables
// no reconocidos (php.exe/launcher.exe, sin firma digital) lean o escriban en
// carpetas fuera de las suyas propias, y el antivirus en tiempo real puede
// hacer lo mismo mientras copia/analiza los archivos recién extraídos.
//
// Se agregan exclusiones de Defender para la carpeta de instalación después de
// copiar los archivos (ssPostInstall, antes de que [Run] intente abrir la app
// por primera vez). Es "best effort": si Defender no es el antivirus activo, si
// hay una política de grupo que lo bloquea, o si Add-MpPreference no existe en
// este Windows, el script simplemente no hace nada - nunca debe impedir que la
// instalación termine ni mostrar un error al usuario.
procedure AddDefenderExclusions();
var
  ResultCode: Integer;
  ScriptPath, AppPath, Script: String;
begin
  AppPath := ExpandConstant('{app}');
  ScriptPath := ExpandConstant('{tmp}\lp360-defender.ps1');

  Script :=
    '$ErrorActionPreference = ''SilentlyContinue''' + #13#10 +
    'try { Add-MpPreference -ExclusionPath "' + AppPath + '" -Force } catch {}' + #13#10 +
    'try { Add-MpPreference -ExclusionProcess "' + AppPath + '\php\php.exe" -Force } catch {}' + #13#10 +
    'try { Add-MpPreference -ExclusionProcess "' + AppPath + '\launcher.exe" -Force } catch {}' + #13#10 +
    'try { Add-MpPreference -ControlledFolderAccessAllowedApplications "' + AppPath + '\php\php.exe" -Force } catch {}' + #13#10 +
    'try { Add-MpPreference -ControlledFolderAccessAllowedApplications "' + AppPath + '\launcher.exe" -Force } catch {}' + #13#10;

  if SaveStringToFile(ScriptPath, Script, False) then
  begin
    Exec(ExpandConstant('{sys}\WindowsPowerShell\v1.0\powershell.exe'),
      '-NoProfile -ExecutionPolicy Bypass -File "' + ScriptPath + '"',
      '', SW_HIDE, ewWaitUntilTerminated, ResultCode);
    DeleteFile(ScriptPath);
  end;
end;

procedure CurStepChanged(CurStep: TSetupStep);
begin
  if CurStep = ssPostInstall then
  begin
    AddDefenderExclusions();
  end;
end;

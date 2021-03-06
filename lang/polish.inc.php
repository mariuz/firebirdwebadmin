<?php
// Purpose        Language strings definitions
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000-2006 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details

//Initial authors
// Dutch          			Michael van Canneyt <Michael.VanCanneyt@Wisa.be
// Japanese       			Shue Miula <shue@xdip.com>
// Polish         			Matthias Hryniszak <matthias@hryniszak.de>
// Hungarian      			Zoltán Miklovicz <zmiklovicz@vivamail.hu>
// Spanish        			Jose Pichardo <joel_pichardo@yahoo.com>
// Russian        			Andrej Surkov <sura@mail.ru>
// Portuguese, Brazilian	Paulo Vaz <paulo@multi-informatica.com.br>

// strings used for the tabfolder menu
$menu_strings = array('Database' => 'Baza danych',
                      'Tables' => 'Tabele',
                      'Accessories' => 'Akcesoria',
                      'SQL' => 'SQL',
                      'Data' => 'Dane',
                      'Users' => 'Użytkownicy',
                      'Admin' => 'Administrator',
                      );

// strings used as panel titles
$ptitle_strings = array('info' => 'Informacje',
                        'db_login' => 'Zaloguj do bazy danych',
                        'db_create' => 'Utwórz bazę danych',
                        'db_delete' => 'Usuń bazę danych',
                        'db_systable' => 'Tabele systemowe',
                        'db_meta' => 'Metadane',
                        'tb_show' => 'Podgląd tabel',
                        'tb_create' => 'Utwórz nową tabelę',
                        'tb_modify' => 'Modyfikuj tabelę',
                        'tb_delete' => 'Usuń tabelę',
                        'acc_index' => 'Indeksy',
                        'acc_gen' => 'Generatory',
                        'acc_trigger' => 'Wyzwalacze',
                        'acc_proc' => 'Procedury',
                        'acc_domain' => 'Domeny',
                        'acc_view' => 'Widoki',
                        'acc_udf' => 'Funkcje użytkownika',
                        'acc_exc' => 'Wyjątki',
                        'sql_enter' => 'Wprowadź polecenie lub skrypt',
                        'sql_output' => 'Pokaż wyniki',
                        'tb_watch' => 'Monitoruj tabelę',
                        'dt_enter' => 'Wprowadź dane',
                        'dt_export' => 'Exportuj Dane',
                        'dt_import' => 'Import CSV',
                        'usr_user' => 'Użytkownicy',
                        'usr_role' => 'Rola',
                        'usr_grant' => 'Uprawnienia',
                        'usr_cust' => 'Dostosuj',
                        'adm_server' => 'Statystyki serwera',
                        'adm_dbstat' => 'Statystyki bazy danych',
                        'adm_gfix' => 'Konserwacja bazy danych',
                        'adm_backup' => 'Kopia zapasowa',
                        'adm_restore' => 'Przywróć z kopii zapasowej',
                        'Open' => 'otwórz',
                        'Close' => 'zamknij',
                        'Up' => 'w górę',
                        'Top' => 'początek',
                        'Bottom' => 'koniec',
                        'Down' => 'dół',
                        'tb_selector' => 'Tables selector',
                        );

// strings to inscribe buttons
$button_strings = array('Login' => 'Użytkownik',
                        'Logout' => 'Wyloguj',
                        'Create' => 'Utwórz',
                        'Delete' => 'Usuń',
                        'Select' => 'Wybierz',
                        'Save' => 'Zapisz',
                        'Reset' => 'Przywróć',
                        'Cancel' => 'Anuluj',
                        'Add' => 'Dodaj',
                        'Modify' => 'Modyfikuj',
                        'Ready' => 'Gotowe',
                        'Yes' => 'Tak',
                        'No' => 'Nie',
                        'DoQuery' => 'Wykonaj kwerendę',
                        'QueryPlan' => 'Plan zapytania',
                        'Go' => 'Idź',
                        'DisplAll' => 'Wyświetl wszystko',
                        'Insert' => 'Wstaw',
                        'Export' => 'Eksportuj',
                        'Import' => 'Importuj',
                        'Remove' => 'Usuń',
                        'Drop' => 'Usuń',
                        'Set' => 'Ustaw',
                        'Clear' => 'Wyczyść',
                        'SweepNow' => 'Uporządkuj teraz',
                        'Execute' => 'Wykonaj',
                        'Backup' => 'Kopia zapasowa',
                        'Restore' => 'Odtwórz',
                        'Reload' => 'Przeładuj',
                        'OpenAll' => 'Otwórz wszystko',
                        'CloseAll' => 'Zamknij wszystko',
                        'Defaults' => 'Ustaw domyślne',
                        'Load' => 'Załaduj',
                        'Unmark' => 'Usuń zaznaczenie',
                        'DropSelectedFields' => 'Drop selected fields',
                        'OpenSelectableMode' => 'Open selectable mode',
                        'DropSelectedTables' => 'Drop selected tables',
                        );

// strings on the database page
$db_strings = array('Database' => 'Baza danych',
                    'Host' => 'Serwer',
                    'Username' => 'Nazwa użytkownika',
                    'Password' => 'Hasło',
                    'Role' => 'Rola',
                    'Cache' => 'Pamięć podręczna',
                    'Charset' => 'Zestaw znaków',
                    'Dialect' => 'Dialekt',
                    'Server' => 'Serwer',
                    'NewDB' => 'Nowe bazy danych',
                    'PageSize' => 'Rozmiar strony',
                    'DelDB' => 'Usuń bazę danych',
                    'SysTables' => 'Tabele systemowe',
                    'SysData' => 'Dane systemowe',
                    'FField' => 'Pole filtrowania',
                    'FValue' => 'Wartość filtra',
                    'Refresh' => 'Odśwież',
                    'Seconds' => 'Sekundy',
                    );

// strings on the table page
$tb_strings = array('Name' => 'Nazwa',
                    'Type' => 'Typ',
                    'Length' => 'Długość',
                    'Prec' => 'Precyzja',
                    'PrecShort' => 'Prec',
                    'Scale' => 'Skala',
                    'Charset' => 'Zestaw znaków',
                    'Collate' => 'Sortowanie',
                    'Collation' => 'Sortowanie',
                    'NotNull' => 'Nie Null',
                    'Unique' => 'Unikalny',
                    'Computed' => 'Wyliczany',
                    'Default' => 'Domyślny',
                    'Primary' => 'Podstawowy',
                    'Foreign' => 'Obcy',
                    'TbName' => 'Nazwa tabeli',
                    'Fields' => 'Pola',
                    'DefColumn' => 'Definicja kolumn',
                    'SelTbMod' => 'Wybierz tabelę do zmodyfikowania',
                    'DefNewCol' => 'Definicja nowej kolumny',
                    'NewColPos' => 'Pozycja nowej kolumny',
                    'SelColDel' => 'Wybierz kolumnę do usunięcia',
                    'SelColMod' => 'Wybierz kolumnę do zmodyfikowania',
                    'AddCol' => 'Dodaj kolumnę',
                    'SelTbDel' => 'Wybierz tabelę do usunięcia',
                    'Datatype' => 'Typ danych',
                    'Size' => 'Rozmiar',
                    'Subtype' => 'Podtyp',
                    'SegSiShort' => 'RozmSeg',
                    'Domain' => 'Domena',
                    'CompBy' => 'Wyliczany przez',
                    'Table' => 'tabela',
                    'Column' => 'kolumna',
                    'Source' => 'Źródło',
                    'Check' => 'Sprawdź',
                    'Yes' => 'Tak',
                    'DispCounts' => 'ilczba rekordów',
                    'DispCNames' => 'ograniczenia',
                    'DispDef' => 'wartości domyślne',
                    'DispComp' => 'wartości obliczane',
                    'DispComm' => 'komentarze',
                    'DropPK' => 'Usuń klucz podstawowy',
                    'DropFK' => 'Usuń klucz obcy',
                    'DropUq' => 'Usuń ograniczenie unikalności',
                    'FKName' => 'Nazwa klucza obcego',
                    'OnUpdate' => 'Przy aktualizacji',
                    'OnDelete' => 'Przy usuwaniu',
                    'Table1' => 'Tabela',
                    'Column1' => 'Kolumna',
                    'DropManyColTitle' => 'Drop columns from table',
                    'TablesActionsTitle' => 'Actions',
                    'WarningManyTables' => 'The actions selected here affects many tables. Make a backup before running this feature.',
                    'Records' => 'Records',
                    'FormTableSelector' => 'Table Selector',
                    'DropManyTables' => 'Drop tables from database',
                    'SQLCommand' => 'SQL Command:',
                );

// strings on the accessories page
$acc_strings = array('CreateIdx' => 'Utwórz nowy indeks',
                     'ModIdx' => 'modyfikuj indeks %s',
                     'Name' => 'Nazwa',
                     'Active' => 'Aktywny',
                     'Unique' => 'Unikalny',
                     'Sort' => 'Sortuj',
                     'Table' => 'Tabela',
                     'Columns' => 'Kolumny',
                     'SelIdxMod' => 'Wybierz indeks do zmodyfikowania',
                     'SelIdxDel' => 'Wybierz indeks do usunięcia',
                     'ColExpl' => 'Kolumna(y) oddzielone przecinkami',
                     'Value' => 'Wartość',
                     'SetValue' => 'Ustaw wartość',
                     'DropGen' => 'Usuń generator',
                     'CreateGen' => 'Utwórz nowy generator',
                     'StartVal' => 'Wartość początkowa',
                     'CreateTrig' => 'Utwórz nowy wyzwalacz',
                     'SelTrigMod' => 'Wybierz wyzwalacz do zmodyfikowania',
                     'SelTrigDel' => 'Wybierz wyzwalacz do usunięcia',
                     'Type' => 'Typ',
                     'Pos' => 'Poz',
                     'Phase' => 'Przed/Po',
                     'Position' => 'Pozycja',
                     'Status' => 'Status',
                     'Source' => 'Źródło',
                     'ModTrig' => 'Modyfikuj wyzwalacz %s',
                     'CreateDom' => 'Utwóz nową domenę',
                     'SelDomDel' => 'Wybierz domenę do usunięcia',
                     'SelDomMod' => 'Wybierz domenę do zmodyfikowania',
                     'Size' => 'Rozmiar',
                     'Charset' => 'Kodowanie',
                     'Collate' => 'Sortowanie',
                     'PrecShort' => 'Prec',
                     'Scale' => 'Skala',
                     'Subtype' => 'Podtyp',
                     'SegSiShort' => 'RozmSeg',
                     'ModDomain' => 'Modyfikuj domenę %s',
                     'Generator' => 'generator',
                     'Index' => 'indeks',
                     'Trigger' => 'wyzwalacz',
                     'Domain' => 'domena',
                     'CreateProc' => 'Utwóz nową procedurę',
                     'ModProc' => 'Modyfikuj procedurę %s',
                     'SelProcMod' => 'Wybierz procedurę do zmodyfikowania',
                     'SelProcDel' => 'Wybierz procedurę do usunięcia',
                     'SP' => 'procedura',
                     'Param' => 'Parametry',
                     'Return' => 'Powrót',
                     'CreateView' => 'Utwórz nowy widok',
                     'SelViewDel' => 'Wybierz widok do usunięcia',
                     'SelViewMod' => 'Wybierz widok do zmodyfikowania',
                     'CheckOpt' => 'z opcją sprawdzania',
                     'ModView' => 'Modyfikuj widok %s',
                     'Yes' => 'Tak',
                     'No' => 'Nie',
                     'Module' => 'Moduł',
                     'EPoint' => 'Punkt wejściowy',
                     'IParams' => 'Parametry wejściowe',
                     'Returns' => 'Zwraca',
                     'UDF' => 'funkcja użytkownika',
                     'SelUdfDel' => 'Wybierz funkcję do usunięcia',
                     'Exception' => 'Wyjątek',
                     'Message' => 'Komunikat',
                     'SelExcDel' => 'Wybierz wyjątek do usunięcia',
                     'CreateExc' => 'Utwórz nowy wyjątek',
                     'SelExcMod' => 'Wybierz wyjątek do zmodyfikowania',
                     'ModExc' => 'Modyfikuj wyjątek %s',
                     );

// strings on the sql page incl. the watch table panel
$sql_strings = array('DisplBuf' => 'wyświetlanie rezultatów z bufora',
                     'SelTable' => 'Wybierz tabelę',
                     'Config' => 'Konfiguracja',
                     'Column' => 'Kolumna',
                     'Show' => 'Pokaż',
                     'Sort' => 'Sortuj',
                     'BlobLink' => 'Blob jako odnośnik',
                     'BlobType' => 'Typ bloba',
                     'Rows' => 'Wiersze',
                     'Start' => 'Start',
                     'Dir' => 'Kierunek',
                     'ELinks' => 'Edytuj odnośniki',
                     'DLinks' => 'Usuń odnośniki',
                     'Asc' => 'Rosnąco',
                     'Desc' => 'Malejąco',
                     'Restrict' => 'Warunek do ograniczenia wyniku, np. FIELDNAME>=1000',
                     'Prev' => 'Poprzedni',
                     'Next' => 'Następny',
                     'End' => 'Koniec',
                     'Total' => 'razem',
                     'Edit' => 'edytuj',
                     'Delete' => 'usuń',
                     'Yes' => 'Tak',
                     'No' => 'Nie',
                     'TBInline' => 'Następny blob inline',
                     'TBChars' => 'Ilość znaków w polach Blob',
                     );

// strings on the data page
$dt_strings = array('SelTable' => 'Wybierz tabelę',
                    'Table' => 'Tabela',
                    'EditFrom' => '%1$sEdytuj z tabeli %2$s',
                    'FileName' => 'Nazwa pliku',
                    'EntName' => 'Podaj nazwę',
                    'FileForm' => 'Format pliku',
                    'ConvEmpty' => 'podczas importu konwertuj puste wartości na NULL',
                    'CsvForm1' => 'wszystkie wartoœci są ujęte w cudzysłowia (") i rozdzielone przecinkami',
                    'CsvForm2' => 'cudzysłoowia w wartościach są podwójne',
                    'CsvForm3' => 'zestawy danych są rozdzielane znakami nowej linii (0x0a)',
                    'ColConf' => 'Konfiguracja dla kolumny %1$s',
                    'ColFKLook' => 'Kolumna dla wyszukiwania klucza obcego',
                    'FKLookup' => 'wyszukiwanie klucza obcego',
                    'IARow' => 'wstaw nowy wiersz',
                    'INRow' => 'wstaw jako nowy wiersz',
                    'Drop' => 'usuń',
                    'ExpOptCsv' => 'Dane CSV',
                    'ExpOptExt' => 'Tabela zewnętrzna',
                    'ExpOptSql' => 'SQL',
                    'ExpFmTbl' => 'Tabela',
                    'ExpFmDb' => 'Baza danych',
                    'ExpFmQry' => 'Zapytanie',
                    'ExpTgFile' => 'Plik',
                    'ExpTgScr' => 'Ekran',
                    'GenOpts' => 'Ustawienia ogólne',
                    'ReplNull' => 'zastąp wartości <i>NULL</i> przez',
                    'DFormat' => 'format daty',
                    'TFormat' => 'format czasu',
                    'CsvOpts' => 'CSV-opcje',
                    'FTerm' => 'pola zakończone przez',
                    'FEncl' => 'pola otoczone przez',
                    'FTEncl' => 'Field types to enclose',
                    'All' => 'wszystskie',
                    'NonNum' => 'nie numeryczny',
                    'FEsc' => 'znak escape',
                    'LTerm' => 'linie zakończone przez',
                    'FNamesF' => 'Field names at first row',
                    'SqlOpts' => 'Opcje SQL',
                    'SqlCNames' => 'dołącz nazwy kolumn',
                    'SqlQNames' => 'cytuj nazwy kolumn',
                    'SqlCField' => 'dołącz kolumny wyliczane',
                    'SqlInfo' => 'dołącz informacje eksportu',
                    'SqlLE' => 'zakończenie linii',
                    'SqlTTab' => 'nazwa tabeli docelowej',
                    'ExtOpts' => 'Opcje tabeli zewnętrznej',
                    'PhpOpts' => 'Opcje źródeł PHP',
                    );

// strings on the user page
$usr_strings = array('CreateUsr' => 'Utwórz nowego u¿ytkownika',
                     'ModUser' => 'Modyfikuj uźytkownika %s',
                     'UName' => 'Nazwa u¿ytkownika',
                     'FName' => 'Imiê',
                     'MName' => 'Drugie imiê',
                     'LName' => 'Nazwisko',
                     'UserID' => 'ID u¿ytkownika',
                     'GroupID' => 'ID grupy',
                     'SysdbaPW' => 'Has³o SYSDBA',
                     'Required' => 'wymagane do operacji tworzenia, modyfikacji i usuniêcia',
                     'USelMod' => 'Wybierz użytkownika do zmodyfikowania',
                     'USelDel' => 'Wybierz użytkownika do usunięcia',
                     'Password' => 'Has³o',
                     'RepeatPW' => 'Has³o ponownie',
                     'Name' => 'Nazwa',
                     'Owner' => 'W³aœciciel',
                     'Members' => 'Cz³onkowie',
                     'Role' => 'Rola',
                     'User' => 'U¿ytkownik',
                     'CreateRole' => 'Utwóz now¹ rolê',
                     'RoleSelDel' => 'Wybierz rolę do usunięcia',
                     'RoleAdd' => 'Dodaj rolę',
                     'RoleRem' => 'Usuń z roli',
                     'ColSet' => 'Ustawienia kolorów',
                     'CBg' => 'Tło',
                     'CPanel' => 'Ramka panelu',
                     'CArea' => 'Tło panelu',
                     'CHeadline' => 'Tło nagłówka',
                     'CMenubrd' => 'Ramka menu',
                     'CIfBorder' => 'Iframe Border',
                     'CIfBg' => 'Iframe Background',
                     'CLink' => 'Odnoœniki',
                     'CHover' => 'Aktywuj odnośniki myszką',
                     'CSelRow' => 'Wybrane wiersze',
                     'CSelInput' => 'Wybrane pola edycyjne',
                     'CFirstRow' => 'Nieparzyste wiersze tabel',
                     'CSecRow' => 'Parzyste wiersze tabel',
                     'Appearance' => 'Wygląd',
                     'Language' => 'Język',
                     'Fontsize' => 'Rozmiar czcionki w punktach',
                     'TACols' => 'Ilść kolumn w polach tekstowych',
                     'TARows' => 'Ilość wierszy w polach tekstowych',
                     'IFHeight' => 'wysokość ramki iframe w pixelach',
                     'Attitude' => 'Zachowanie',
                     'AskDel' => 'Potwierdzaj usunięcie obiektów i danych',
                     'Yes' => 'Tak',
                     'No' => 'Nie',
                    );

// strings on the admin page
$adm_strings = array('SysdbaPW' => 'Hasło SYSDBA',
                     'Required' => 'wymagane, jeśli nie jesteś właścicielem bazy danych',
                     'Sweeping' => 'Czyszczenie',
                     'SetInterv' => 'Ustaw próg czyszczenia (liczba transakcji)',
                     'DBDialect' => 'Dialekt',
                     'Buffers' => 'Bufory pamięci podręcznej',
                     'AccessMode' => 'Tryb dostępu',
                     'WriteMode' => 'Tryb zapisu',
                     'ReadWrite' => 'odczyt/zapis',
                     'ReadOnly' => 'tylko do odczytu',
                     'Sync' => 'synchroniczne',
                     'Async' => 'asynchroniczne',
                     'UseSpace' => 'Użyj miejsca',
                     'SmallFull' => 'pełne',
                     'Reserve' => 'rezerwy',
                     'DataRepair' => 'Naprawianie danych',
                     'Validate' => 'Walidacja',
                     'Full' => 'Pełny',
                     'Mend' => 'Napraw',
                     'NoUpdate' => 'Bez aktualizacji',
                     'IgnoreChk' => 'Ignoruj błędy sum kontrolnych',
                     'Transact' => 'Transakcje',
                     'Shutdown' => 'Zamknij',
                     'Commit' => 'Zatwierdź',
                     'Rollback' => 'Wycofaj',
                     'TwoPhase' => 'Odzyskiwanie dwufazowe',
                     'AllLimbo' => 'wszystkie zagubione transakcje',
                     'NoConns' => 'Brak nowych połączeń',
                     'NoTrans' => 'Brak nowych transakcji',
                     'Force' => 'Wymuś',
                     'ForSeconds' => 'na/po %s sek.',
                     'Reconnect' => 'Połącz ponownie FirebirdWebAdmin po zamknięciu',
                     'Rescind' => 'Unieważnij zamknięcie',
                     'BTarget' => 'Lokacja kopii zapasowych',
                     'FDName' => 'Nazwa pliku lub urządzenia',
                     'Options' => 'Opcje',
                     'BConvert' => 'Konwertuj zewnêtrzne pliki jako wewnêtrzne tabele',
                     'BNoGC' => 'Nie odzyskuj miejsca podczas wykonywania kopii zapasowej',
                     'BIgnoreCS' => 'Ignoruj b³êdy sumy kontrolnej podczas wykonywania kopii zapasowej',
                     'BIgnoreLT' => 'Ignoruj zagubione transkacje podczas wykonywania kopii zapasowej',
                     'BTransport' => 'Wykonaj kopię bezpieczeństwa do formatu nieprzenaszalnego',
                     'BMDOnly' => 'Wykonaj kopiê zapasow¹ metadanych',
                     'BMDOStyle' => 'Struktura bazy danych w starym stylu',
                     'RSource' => 'Przywróæ Ÿród³o',
                     'RTarget' => 'Docelowe miejsce odtwarzania',
                     'TargetDB' => 'Docelowa baza danych',
                     'NewFile' => 'Przywróæ do nowego pliku',
                     'RestFile' => 'Zamieñ istniej¹cy plik',
                     'PageSize' => 'Rozmiar strony',
                     'UseAll' => 'Przywróć bazę danych ze 100% wypełnieniem każdej strony danych',
                     'OneAtTime' => 'Przywróæ jedn¹ tabelê na raz',
                     'IdxInact' => 'Deaktywuj Indeksy podczas przywracaniu',
                     'NoValidity' => 'Usuñ ograniczenia walidacji z przywróconych metadanych',
                     'KillShad' => 'Nie twórz poprzednio utworzonych plików cieni',
                     'ConnAfter' => 'Pod³¹cz FirebirdWebAdmin do przywróconej bazy danych',
                     'Verbose' => 'Szczegóły',
                     'Analyze' => 'Analyze',
                     );

// strings for the info-panel
$info_strings = array('Connected' => 'Połączony z bazą danych',
                      'ExtResult' => 'Rezultat zewnętrznego polecenia',
                      'FBError' => 'Błąd Firebird',
                      'ExtError' => 'Błąd z zewnętrznego polecenia',
                      'Error' => 'B³¹d',
                      'Warning' => 'Ostrze¿enie',
                      'Message' => 'Wiadomoœæ',
                      'ComCall' => 'Wywo³anie polecenia',
                      'Debug' => 'Wyjœcie debuggera',
                      'PHPError' => 'B³¹d PHP',
                      'SuccessLogin' => 'Zostałeś pomyślnie zalogowany!',
                      );

$MESSAGES = array('SP_CREATE_INFO' => 'FirebirdWebAdmin utworzy³ zapamiêtan¹ procedurê "'.SP_LIMIT_NAME.'" która jest u¿ywana przy funkcji monitorowania tabel '
                                            ."i zapisa³ j¹ w twojej bazie danych.<br>\n"
                                            .'Je¿eli wiele osób ko¿ysta z FirebirdWebAdmin w tym samym czasie, zmieñ wartoœæ '
                                            ."WATCHTABLE_METHOD w pliku inc/configuration.inc.php.<br>\n",
                  'EDIT_ADD_PRIMARY' => "Jeśli edycja jest włączona, pola klucza podstawowego musi być zaznaczone do pokazywania w konfiguracji monitorowania tabeli.<br>\n"
                                            .'Program automatycznie zaznaczył pola indeksu podstawowego.',
                  'CSV_IMPORT_COUNT' => '%1$d wierszy zaimportowano do tabeli %2$s<br>',
                  'CONFIRM_TABLE_DELETE' => 'Czy na pewno chcesz usun¹æ tabelê %s?',
                  'CONFIRM_COLUMN_DELETE' => 'Czy na pewno chcesz usun¹æ kolumnê %1$s z tabeli %2$s?',
                  'CONFIRM_DB_DELETE' => 'Czy na pewno chcesz usun¹æ bazê danych %s?',
                  'CONFIRM_TRIGGER_DELETE' => 'Czy na pewno chcesz usun¹æ wyzwalacz %s?',
                  'CONFIRM_DOMAIN_DELETE' => 'Czy na pewno chcesz usun¹æ domenê %s?',
                  'CONFIRM_INDEX_DELETE' => 'Czy na pewno chcesz usun¹æ indeks %s?',
                  'CONFIRM_GEN_DELETE' => 'Czy na pewno chcesz usun¹æ generator %s?',
                  'CONFIRM_USER_DELETE' => 'Czy na pewno chcesz usun¹æ u¿ytkownika %s?',
                  'CONFIRM_ROW_DELETE' => 'Czy na pewno chcesz usun¹æ dane z tabeli %s %s?',
                  'CONFIRM_SP_DELETE' => 'Czy na pewno chcesz usun¹æ zapamiêtan¹ procedurê %s?',
                  'CONFIRM_VIEW_DELETE' => 'Czy na pewno chcesz usun¹æ widok %s?',
                  'CONFIRM_UDF_DELETE' => 'Czy na pewno chcesz usun¹æ funkcjê %s?',
                  'CONFIRM_EXC_DELETE' => 'Czy na pewno chcesz usunąć wyjątek %s?',
                  'NO_VIEW_SUPPORT' => "Edytowanie i usuwanie z widoków nie jest wspierane aż do teraz.<br>\n",
                  'CREATE_DB_SUCCESS' => "Baza danych %s Zosta³a poprawnie utworzona.\n",
                  'HAVE_DEPENDENCIES' => 'Musisz najpierw usun¹æ nastêpuj¹ce obiekty zanim bêdziesz móg³ usun¹æ %1$(y) %2$(y): %3$(y)',
                  'COOKIES_NEEDED' => 'Musisz włączyć obsługę plików cookie w ustawieniach przeglądarki, jeśli chcesz korzystać z funkcji dostosowywania!',
                  'CONFIRM_MANY_TABLES_DELETE' => 'Do you want to permanently remove these tables?',
                  'CONFIRM_MANY_COLUMNS_DELETE' => 'Do you want to permanently remove these columns from the table?',
                  );

$WARNINGS = array('CAN_NOT_EXPORT_BLOBS' => "Pola Blob, które zostały przez ciebie zaznaczone zostały pominięte.<br>\n"
                                            ."Eksport Blobów jest wspierany tylko do tekstowego formatu CSV.<br>\n",
                  'CAN_NOT_IMPORT_BLOBS' => "Pola Blob, które zostały przez ciebie zaznaczone zostały pominięte.<br>\n"
                                            ."Import Blobów jest wspierany tylko z tekstowego formatu CSV.<br>\n",
                  'SELECT_TABLE_FIRST' => "Najpierw wybierz tabelę<br>\n",
                  'SELECT_FILE_FIRST' => "Najpierw wybierz plik importu<br>\n",
                  'CAN_NOT_ALTER_DOMAINS' => "Zmiana kolumn bazuj¹cych na domenach nie jest wspierana przez Firebird.<br>\n"
                                            ."W zamian za to zmieñ definicjê domeny na zak³adce 'Akcesoria'.<br>\n",
                  'CAN_NOT_EDIT_TABLE' => "Edycja wybranej tabeli nie jest możliwa.<br>\n"
                                            ."Tylko tabele z kluczem g³ównym s¹ edytowalne.<br>\n",
                  'CAN_NOT_DEL_TABLE' => "Usuwanie z wybranej tabeli nie jest możliwe.<br>\n"
                                            ."Usuwaæ wiersze mo¿na tylko z tabel, które posiadaj¹ podstawowy indeks.<br>\n",
                  'DEL_NO_PERMISSON' => "Nie masz uprawnieñ do usuwania/zapisu w tabeli %s<br>\n",
                  'EDIT_NO_PERMISSON' => "Nie masz uprawnieñ do aktualizacji/zapisu tabeli %s<br>\n",
                  'CAN_NOT_ACCESS_DIR' => "Nie powiod³a siê próba dostêpu do folderu %s<br>\n",
                  'NAME_IS_KEYWORD' => "%s jest zarezerwowanym s³owem kluczowym Firebird<br>\n",
                  'NAMES_ARE_KEYWORDS' => "%s s¹ zarezerwowanymi s³owami kluczowymi Firebird<br>\n",
                  'NEED_SYSDBA_PW' => "Has³o SYSDBA's jest wymagane do utworzenia, modyfikacji lub usuwania u¿ytkowników.<br>\n",
                  'PW_REQUIRED' => "Hasło jest wymagane<br>\n",
                  'UN_REQUIRED' => "Nazwa u¿ytkownika jest wymagana<br>\n",
                  'PW_WRONG_REPEAT' => "Has³o jest niepoprawne.<br>\n",
                  'BAD_ISQLPATH' => "W folderze %s nie ma programu isql!<br>\n"
                                            ."Proszę sprawdzć wartość zmiennej BINPATH w pliku inc/configuration.inc.php.<br>\n",
                  'BAD_TMPPATH' => "Podana ścieżka TMPPATH '%s' nie istnieje lub nie jest do zapisu przez proces serwera www!<br>\n"
                                            ."Proszę sprawdzć wartość zmiennej BINPATH w pliku inc/configuration.inc.php.<br>\n",
                  );

$ERRORS = array('CREATE_DB_FAILED' => 'Tworzenie bazy danych <b>%s</b> nie powiodło się!',
                  'NO_DB_SELECTED' => 'Najpierw wybierz nazwę bazy danych!<br>',
                  'WRONG_DB_SUFFIX' => 'Nazwa bazy danych musi mieć rozszerzenie <b>%s</b>',
                  'DB_NOT_ALLOWED' => 'Dostęp do <b>%s</b> jest zabroniony.<br>'
                                             .'(sprawdź $ALLOWED_FILES i $ALLOWED_DIRS w pliku inc/configuration.php)',
                  'NO_IBASE_MODULE' => '<b>Twoja instalacja php nie ma obsługi Fierbird/InterBase!</b><br>'
                                            .'Przekompiluj PHP i skonfiguruj z --with-interbase[=DIR]<br>'
                                            .'lub zmień plik php.ini aby wczytać interbase.so względnie interbase.dll.',
                  'DISABLED_CMD' => 'Kwerendy SQL zawierające "%s" są zabronione przez konfigurację!',
                  'BAD_BINPATH' => "Nie można odnaleźć polecenia <b>%s</b>! \n"
                                            ."Sprawdź wartość dla BINPATH w inc/configuration.inc.php.\n",
                  );

// charset encoding  for html output
$charset = 'UTF-8';

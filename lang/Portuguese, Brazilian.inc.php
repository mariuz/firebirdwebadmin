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
$menu_strings = array('Database' => 'Banco de Dados',
                      'Tables' => 'Tabelas',
                      'Accessories' => 'Acessórios',
                      'SQL' => 'SQL',
                      'Data' => 'Dados',
                      'Users' => 'Usuários',
                      'Admin' => 'Administração',
                      );

// strings used as panel titles
$ptitle_strings = array('info' => 'Informações',
                        'db_login' => 'Logar-se ao Banco de Dados',
                        'db_create' => 'Criar Banco de Dados',
                        'db_delete' => 'Apagar Banco de Dados',
                        'db_systable' => 'Tabelas de Sistema',
                        'db_meta' => 'Metadados',
                        'tb_show' => 'Ver Tabelas',
                        'tb_create' => 'Criar Nova Tabela',
                        'tb_modify' => 'Modificar Tabela',
                        'tb_delete' => 'Apagar Tabela',
                        'acc_index' => 'Índices',
                        'acc_gen' => 'Geradores',
                        'acc_trigger' => 'Disparadores',
                        'acc_proc' => 'Procedimentos armazenados',
                        'acc_domain' => 'Domínios',
                        'acc_view' => 'Visualizações',
                        'acc_udf' => 'Funções definidos pelo usuário',
                        'acc_exc' => 'Exceções',
                        'sql_enter' => 'Entre com o Comando ou Script',
                        'sql_output' => 'Mostrar Resultado',
                        'tb_watch' => 'Ver Tabela',
                        'dt_enter' => 'Inserir Dados',
                        'dt_export' => 'Exportar dados',
                        'dt_import' => 'Importar CSV',
                        'usr_user' => 'Usuários',
                        'usr_role' => 'Regras',
                        'usr_grant' => 'Permissões',
                        'usr_cust' => 'Personalizando',
                        'adm_server' => 'Estatísticas do Servidor',
                        'adm_dbstat' => 'Estatísticas do Banco de Dados',
                        'adm_gfix' => 'Manutenção de banco de dados',
                        'adm_backup' => 'Cópia de segurança',
                        'adm_restore' => 'Restauração',
                        'Open' => 'abrir',
                        'Close' => 'fechar',
                        'Up' => 'para cima',
                        'Top' => 'topo',
                        'Bottom' => 'rodapé',
                        'Down' => 'para baixo',
                        'tb_selector' => 'Seletor de tabelas',
                        );

// strings to inscribe buttons
$button_strings = array('Login' => 'Acesso',
                        'Logout' => 'Desconectar',
                        'Create' => 'Criar',
                        'Delete' => 'apagar',
                        'Select' => 'Selecionar',
                        'Save' => 'Salvar',
                        'Reset' => 'Recomeçar',
                        'Cancel' => 'Cancelar',
                        'Add' => 'Adicionar',
                        'Modify' => 'Modificar',
                        'Ready' => 'Pronto',
                        'Yes' => 'Sim',
                        'No' => 'Não',
                        'DoQuery' => 'Executar Query',
                        'QueryPlan' => 'Plano da Query',
                        'Go' => 'Ir',
                        'DisplAll' => 'Mostrar tudo',
                        'Insert' => 'Inserir',
                        'Export' => 'Exportar',
                        'Import' => 'Importar',
                        'Remove' => 'Remover',
                        'Drop' => 'Apagar',
                        'Set' => 'Definir',
                        'Clear' => 'Limpar',
                        'SweepNow' => 'Limpar agora',
                        'Execute' => 'Executar',
                        'Backup' => 'Cópia de segurança',
                        'Restore' => 'Restauração',
                        'Reload' => 'Recarregar',
                        'OpenAll' => 'Abrir Todas',
                        'CloseAll' => 'Fechar Todos',
                        'Defaults' => 'Definir padrões',
                        'Load' => 'Carregar',
                        'Unmark' => 'Desmarcar',
                        'DropSelectedFields' => 'Apagar campos selecionados',
                        'OpenSelectableMode' => 'Abrir modo de seleção de tabelas',
                        'DropSelectedTables' => 'Apagar tabelas selecionadas',
                        );

// strings on the database page
$db_strings = array('Database' => 'Banco de Dados',
                    'Host' => 'Servidor',
                    'Username' => 'Nome do Usuário',
                    'Password' => 'Senha',
                    'Role' => 'Papel',
                    'Cache' => 'Cache',
                    'Charset' => 'Conjunto de caracteres',
                    'Dialect' => 'Dialeto',
                    'Server' => 'Servidor',
                    'NewDB' => 'Novo Banco de Dados',
                    'PageSize' => 'Tamanho de Página',
                    'DelDB' => 'Apagar Banco de Dados',
                    'SysTables' => 'Tabelas de Sistema',
                    'SysData' => 'Dados do Sistema',
                    'FField' => 'Campo de filtro',
                    'FValue' => 'Valor do filtro',
                    'Refresh' => 'Atualização',
                    'Seconds' => 'Segundos',
                    );

// strings on the table page
$tb_strings = array('Name' => 'Nome',
                    'Type' => 'Tipo',
                    'Length' => 'Tamanho',
                    'Prec' => 'Precisão',
                    'PrecShort' => 'Prec',
                    'Scale' => 'Escala',
                    'Charset' => 'Definir caractere',
                    'Collate' => 'Agrupar',
                    'Collation' => 'Agrupamento',
                    'NotNull' => 'Não Nulo',
                    'Unique' => 'Uníco',
                    'Computed' => 'Calculado',
                    'Default' => 'Padrão',
                    'Primary' => 'Primário',
                    'Foreign' => 'Estrangeiro',
                    'TbName' => 'Nome da Tabela',
                    'Fields' => 'Campos',
                    'DefColumn' => 'Definitições para a Coluna',
                    'SelTbMod' => 'Selecione a tabela para modificar',
                    'DefNewCol' => 'Definições para a Nova Coluna',
                    'NewColPos' => 'Nova Posição para a Coluna',
                    'SelColDel' => 'Selecione uma coluna para apagar',
                    'SelColMod' => 'Selecione uma coluna para modificar',
                    'AddCol' => 'Adicionar coluna',
                    'SelTbDel' => 'Selecione uma tabela para apagar',
                    'Datatype' => 'Tipo de Dado',
                    'Size' => 'Tamanho',
                    'Subtype' => 'Subtipo',
                    'SegSiShort' => 'Tamanho de Segmento',
                    'Domain' => 'Domínio',
                    'CompBy' => 'Calculado por',
                    'Table' => 'tabela',
                    'Column' => 'coluna',
                    'Source' => 'Origem',
                    'Check' => 'Check',
                    'Yes' => 'Sim',
                    'DispCounts' => 'display record counts',
                    'DispCNames' => 'constraint names',
                    'DispDef' => 'default values',
                    'DispComp' => 'computed values',
                    'DispComm' => 'comentários',
                    'DropPK' => 'Drop Primary Key',
                    'DropFK' => 'Drop Foreign Key',
                    'DropUq' => 'Drop Unique Constraint',
                    'FKName' => 'Foreign Key Name',
                    'OnUpdate' => 'On Update',
                    'OnDelete' => 'On Delete',
                    'Table1' => 'Table',
                    'Column1' => 'Column',
                    'DropManyColTitle' => 'Remover colunas da tabela',
                    'TablesActionsTitle' => 'Ações',
                    'WarningManyTables' => 'As ações disponíveis aqui afetam várias tabelas. Faça um backup antes de realizar alguma ação.',
                    'Records' => 'Registros',
                    'FormTableSelector' => 'Tabelas selecionáveis',
                    'DropManyTables' => 'Remover as tabelas do banco de dados',
                    'SQLCommand' => 'Comando SQL:',
                    );

// strings on the accessories page
$acc_strings = array('CreateIdx' => 'Criar Novo Índice',
                     'ModIdx' => 'Modificar Índice %s',
                     'Name' => 'Nome',
                     'Active' => 'Ativo',
                     'Unique' => 'Único',
                     'Sort' => 'Classificar',
                     'Table' => 'Tabela',
                     'Columns' => 'Colunas',
                     'SelIdxMod' => 'Selecione um índice para modificar',
                     'SelIdxDel' => 'Selecione um índice para apagar',
                     'ColExpl' => 'Coluna(s), separadas por vírgula',
                     'Value' => 'Valor',
                     'SetValue' => 'Definir Valor',
                     'DropGen' => 'Remover Generator',
                     'CreateGen' => 'Criar Novo Generator',
                     'StartVal' => 'Valor Inicial',
                     'CreateTrig' => 'Criar Nova Trigger',
                     'SelTrigMod' => 'Selecione uma trigger para modificar',
                     'SelTrigDel' => 'Selecione uma trigger para apagar',
                     'Type' => 'Tipo',
                     'Pos' => 'Pos',
                     'Phase' => 'Fase',
                     'Position' => 'Posição',
                     'Status' => 'Situação Atual',
                     'Source' => 'Origem',
                     'ModTrig' => 'Modificar Trigger %s',
                     'CreateDom' => 'Criar Novo Domínio',
                     'SelDomDel' => 'Selecione domínio para apagar',
                     'SelDomMod' => 'Selecione domínio para modificar',
                     'Size' => 'Tamanho',
                     'Charset' => 'Charset',
                     'Collate' => 'Collate',
                     'PrecShort' => 'Prec',
                     'Scale' => 'Escala',
                     'Subtype' => 'Subtipo',
                     'SegSiShort' => 'Tamanho do Segmento',
                     'ModDomain' => 'Modificar Domínio %s',
                     'Generator' => 'generator',
                     'Index' => 'índice',
                     'Trigger' => 'trigger',
                     'Domain' => 'domínio',
                     'CreateProc' => 'Criar Nova Procedure',
                     'ModProc' => 'Modificar Procedure %s',
                     'SelProcMod' => 'Selecione uma procedure para modificar',
                     'SelProcDel' => 'Selecione uma procedure para apagar',
                     'SP' => 'stored procedure',
                     'Param' => 'Parâmetros',
                     'Return' => 'Retorno',
                     'CreateView' => 'Create New View',
                     'SelViewDel' => 'Select view to delete',
                     'SelViewMod' => 'Select view to modify',
                     'CheckOpt' => 'with check option',
                     'ModView' => 'Modify View %s',
                     'Yes' => 'Sim',
                     'No' => 'Não',
                     'Module' => 'Module',
                     'EPoint' => 'Entrypoint',
                     'IParams' => 'Input Parameters',
                     'Returns' => 'Returns',
                     'UDF' => 'user defined function',
                     'SelUdfDel' => 'Select function to delete',
                     'Exception' => 'Exception',
                     'Message' => 'Message',
                     'SelExcDel' => 'Select exception to delete',
                     'CreateExc' => 'Create new exception',
                     'SelExcMod' => 'Select exception to modify',
                     'ModExc' => 'Modify exception %s',
                     );

// strings on the sql page incl. the watch table panel
$sql_strings = array('DisplBuf' => 'mostrando resultado do buffer',
                     'SelTable' => 'Selecione uma Tabela',
                     'Config' => 'Configurar',
                     'Column' => 'Coluna',
                     'Show' => 'Mostrar',
                     'Sort' => 'Classificar',
                     'BlobLink' => 'Blob como Link',
                     'BlobType' => 'Tipo de Blob',
                     'Rows' => 'Linhas',
                     'Start' => 'Iniciar',
                     'Dir' => 'Direção',
                     'ELinks' => 'Editar Links',
                     'DLinks' => 'Apagar Links',
                     'Asc' => 'Ascendente',
                     'Desc' => 'Descendente',
                     'Restrict' => 'Condição para restringir o resultado, ex. NOMEDOCAMPO>=1000',
                     'Prev' => 'Anterior',
                     'Next' => 'Próximo',
                     'End' => 'Fim',
                     'Total' => 'total',
                     'Edit' => 'editar',
                     'Delete' => 'apagar',
                     'Yes' => 'Sim',
                     'No' => 'Não',
                     'TBInline' => 'Text Blobs Inline',
                     'TBChars' => 'Text Blob Characters',
                     );

// strings on the data page
$dt_strings = array('SelTable' => 'Selecione uma Tabela',
                    'Table' => 'Tabela',
                    'EditFrom' => '%1$sEdit da tabela %2$s',
                    'FileName' => 'Nome do Arquivo',
                    'EntName' => 'Informe o Nome',
                    'FileForm' => 'Formato do Arquivo',
                    'ConvEmpty' => 'on import convert empty values to NULL',
                    'CsvForm1' => 'todos os valores entre aspas (") e separados por vírgulas',
                    'CsvForm2' => 'valores entre aspas duplas',
                    'CsvForm3' => 'data sets são separados por novas linhas(0x0a)',
                    'ColConf' => 'Configuration for Column %1$s',
                    'ColFKLook' => 'Column for foreign key lookup',
                    'FKLookup' => 'foreign key lookup',
                    'IARow' => 'insert another row',
                    'INRow' => 'insert as a new row',
                    'Drop' => 'drop',
                    'ExpOptCsv' => 'CSV-Data',
                    'ExpOptExt' => 'External Table',
                    'ExpOptSql' => 'SQL',
                    'ExpFmTbl' => 'Table',
                    'ExpFmDb' => 'Database',
                    'ExpFmQry' => 'Query',
                    'ExpTgFile' => 'File',
                    'ExpTgScr' => 'Screen',
                    'GenOpts' => 'General Options',
                    'ReplNull' => 'replace <i>NULL</i> values by',
                    'DFormat' => 'date format',
                    'TFormat' => 'time format',
                    'CsvOpts' => 'CSV-Options',
                    'FTerm' => 'fields terminated by',
                    'FEncl' => 'fields enclosed by',
                    'FTEncl' => 'field types to enclose',
                    'All' => 'all',
                    'NonNum' => 'não numéricos',
                    'FEsc' => 'escape character',
                    'LTerm' => 'lines terminated by',
                    'FNamesF' => 'field names at first row',
                    'SqlOpts' => 'Opções SQL',
                    'SqlCNames' => 'incluir nomes das colunas',
                    'SqlQNames' => 'aspas em nomes de colunas',
                    'SqlCField' => 'incluir campos computados',
                    'SqlInfo' => 'adicionar informação de exportação',
                    'SqlLE' => 'final de linha',
                    'SqlTTab' => 'nome da tabela de destino',
                    'ExtOpts' => 'Opções de tabela externa',
                    'PhpOpts' => 'Opções de fonte do PHP',
                    );

// strings on the user page
$usr_strings = array('CreateUsr' => 'Criar Novo Usuário',
                     'ModUser' => 'Modificar Usuário %s',
                     'UName' => 'Nome no Usuário',
                     'FName' => 'Primeiro Nome',
                     'MName' => 'Sobrenome Intermediário',
                     'LName' => 'Último Sobrenome',
                     'UserID' => 'ID do Usuário',
                     'GroupID' => 'ID do Grupo',
                     'SysdbaPW' => 'senha do SYSDBA',
                     'Required' => 'requerido para criar, modificar e apagar',
                     'USelMod' => 'Selecione um usuário para modificar',
                     'USelDel' => 'Selecione um usuário para apagar',
                     'Password' => 'Senha',
                     'RepeatPW' => 'Senha (Repetir)',
                     'Name' => 'Nome',
                     'Owner' => 'Dono',
                     'Members' => 'Membros',
                     'Role' => 'Role',
                     'User' => 'Usuário',
                     'CreateRole' => 'Criar uma nova role',
                     'RoleSelDel' => 'Selecione uma role para apagar',
                     'RoleAdd' => 'Adicionar na role',
                     'RoleRem' => 'Remover da role',
                     'ColSet' => 'Color Settings',
                     'CBg' => 'Background',
                     'CPanel' => 'Panel Frame',
                     'CArea' => 'Panel Background',
                     'CHeadline' => 'Headline Background',
                     'CMenubrd' => 'Menuborder',
                     'CIfBorder' => 'Iframe Border',
                     'CIfBg' => 'Iframe Background',
                     'CLink' => 'Links',
                     'CHover' => 'Links while Mouseover',
                     'CSelRow' => 'Selected Rows',
                     'CSelInput' => 'Campos de entrada selecionados',
                     'CFirstRow' => 'Linhas impares da tabela',
                     'CSecRow' => 'Linhas pares da tabela',
                     'Appearance' => 'Aparência',
                     'Language' => 'Idioma',
                     'Fontsize' => 'Tamanho da fonte em pontos',
                     'TACols' => 'Colunas do campo de textarea',
                     'TARows' => 'Linhas do campo textarea',
                     'IFHeight' => 'Altura do iframe em Pixel',
                     'Attitude' => 'Atitude',
                     'AskDel' => 'Confirmar exclusão de objetos e dados',
                     'Yes' => 'Sim',
                     'No' => 'Não',
                    );

// strings on the admin page
$adm_strings = array('SysdbaPW' => 'senha do SYSDBA',
                     'Required' => 'necessário se você não for o proprietário do banco de dados',
                     'Sweeping' => 'A varrer',
                     'SetInterv' => 'Limiar de varredura definido (números transações)',
                     'DBDialect' => 'Dialeto de DB',
                     'Buffers' => 'Buffers de cache',
                     'AccessMode' => 'Modo de acesso',
                     'WriteMode' => 'Modo de gravação',
                     'ReadWrite' => 'leitura/gravação',
                     'ReadOnly' => 'somente leitura',
                     'Sync' => 'síncrono',
                     'Async' => 'assíncrono',
                     'UseSpace' => 'Espaço de uso',
                     'SmallFull' => 'máximo',
                     'Reserve' => 'reserva',
                     'DataRepair' => 'Reparação de dados',
                     'Validate' => 'Validade',
                     'Full' => 'Cheio',
                     'Mend' => 'Emendar',
                     'NoUpdate' => 'Nenhuma atualização',
                     'IgnoreChk' => 'Ignorar erros do Checksum',
                     'Transact' => 'Transações',
                     'Shutdown' => 'Desligar',
                     'Commit' => 'Aplicar',
                     'Rollback' => 'Reverter',
                     'TwoPhase' => 'Recuperação de duas fases',
                     'AllLimbo' => 'todas as transações de limbo',
                     'NoConns' => 'Não há novas conexões',
                     'NoTrans' => 'Não há novas transações',
                     'Force' => 'Forçar',
                     'ForSeconds' => 'para/depois %s segundos',
                     'Reconnect' => 'Reconectar-se FirebirdWebAdmin após o desligamento',
                     'Rescind' => 'Rescindir o desligamento',
                     'BTarget' => 'Destino de backup',
                     'FDName' => 'Nome do arquivo ou dispositivo',
                     'Options' => 'Opções',
                     'BConvert' => 'Converter arquivos externos como tabelas internas',
                     'BNoGC' => 'Não fazer coleta de lixo durante o backup',
                     'BIgnoreCS' => 'Ignorar Checksums durante o backup',
                     'BIgnoreLT' => 'Ignorar as transações de limbo durante o backup',
                     'BTransport' => 'Backup in non transportable format',
                     'BMDOnly' => 'Backup only metadata',
                     'BMDOStyle' => 'Metadata in old-style format',
                     'RSource' => 'Restore Source',
                     'RTarget' => 'Restore Target',
                     'TargetDB' => 'Target database',
                     'NewFile' => 'Restore to new file',
                     'RestFile' => 'Replace existing file',
                     'PageSize' => 'Page Size',
                     'UseAll' => 'Restore Database with 100% fill ratio on every data page',
                     'OneAtTime' => 'Restore one table at a time',
                     'IdxInact' => 'Indexes inactive upon restore',
                     'NoValidity' => 'Delete validity constraints from restored metadata',
                     'KillShad' => 'Do not create previously defined shadow files',
                     'ConnAfter' => 'Connect FirebirdWebAdmin to the restored database',
                     'Verbose' => 'Verbose',
                     'Analyze' => 'Analyze',
                     );

// strings for the info-panel
$info_strings = array('Connected' => 'conectado ao Banco de Dados',
                      'ExtResult' => 'Resultado de um comando externo',
                      'FBError' => 'Erro do Firebird',
                      'ExtError' => 'Erro de um comando externo',
                      'Error' => 'Erro',
                      'Warning' => 'Atenção',
                      'Message' => 'Mensagem',
                      'ComCall' => 'Chamada à um Comando',
                      'Debug' => 'Saída do Debug',
                      'PHPError' => 'PHP Error',
                      'SuccessLogin' => 'Você foi conectado com sucesso!',
                      );

$MESSAGES = array('SP_CREATE_INFO' => 'FirebirdWebAdmin criou uma stored procedure "'.SP_LIMIT_NAME.'" que será utilizada pela função Ver Tabela '
                                            ."e salvou isto em seu Banco de Dados.<br>\n"
                                            .'Se muitas pessoas estiverem usando o FirebirdWebAdmin ao mesmo tempo, por favor mude o valor '
                                            ."de WATCHTABLE_METHOD no arquivo inc/configuration.inc.php.<br>\n",
                  'EDIT_ADD_PRIMARY' => "Se a edição estiver habilitada os campos da chave primária devem ser selecionados para exibição na configuração de Ver Tabela.<br>\n"
                                            .'O programa fez uma auto-selecção dos campos de índices primários necessários.',
                  'CSV_IMPORT_COUNT' => '%1$d linhas importadas para a tabela %2$s<br>',
                  'CONFIRM_TABLE_DELETE' => 'Você realmente deseja apagar a tabela %s?',
                  'CONFIRM_COLUMN_DELETE' => 'Você realmente deseja apagar a coluna %1$s da tabela %2$s?',
                  'CONFIRM_DB_DELETE' => 'Você realmente deseja apagar o banco de dados %s?',
                  'CONFIRM_TRIGGER_DELETE' => 'Você realmente deseja apagar a trigger %s?',
                  'CONFIRM_DOMAIN_DELETE' => 'Você realmente deseja apagar o domínio %s?',
                  'CONFIRM_INDEX_DELETE' => 'Você realmente deseja apagar o índice %s?',
                  'CONFIRM_GEN_DELETE' => 'Você realmente deseja apagar o generator %s?',
                  'CONFIRM_USER_DELETE' => 'Você realmente deseja apagar o usuário %s?',
                  'CONFIRM_ROW_DELETE' => 'Você realmente deseja apagar da tabela %s %s?',
                  'CONFIRM_SP_DELETE' => 'Você realmente deseja apagar a stored procedure %s?',
                  'CONFIRM_VIEW_DELETE' => 'Do you really want to delete the view %s?',
                  'CONFIRM_UDF_DELETE' => 'Do you really want to delete the function %s?',
                  'CONFIRM_EXC_DELETE' => 'Do you really want to delete the exception %s?',                  
                  'NO_VIEW_SUPPORT' => "Editar ou apagar a partir de Views não é atualmente possível.<br>\n",
                  'CREATE_DB_SUCCESS' => "Banco de Dados %s criado com sucesso.\n",
                  'HAVE_DEPENDENCIES' => 'Você precisa apagar os seguintes objetos antes de apagar %1$s %2$s: %3$s',
                  'COOKIES_NEEDED' => 'You have to enable cookies in your browser settings if you want to use the customizing feature!',
                  'CONFIRM_MANY_TABLES_DELETE' => 'Você deseja remover permanentemente estas tabelas do banco de dados?',
                  'CONFIRM_MANY_COLUMNS_DELETE' => 'Você deseja remover permanentemente estas colunas da tabela?',
                  );

$WARNINGS = array('CAN_NOT_EXPORT_BLOBS' => "Os campos Blob na tabela que você selecionou estão omitidos.<br>\n"
                                            ."Somente exportação de Blobs do tipo texto são suportados na exportação csv.<br>\n",
                  'CAN_NOT_IMPORT_BLOBS' => "The blob fields in the table you have selected are omitted.<br>\n"
                                            ."Somente a importação de Blobs texto é possível no csv.<br>\n",
                  'SELECT_TABLE_FIRST' => "Por favor selecione uma tabela primeiro<br>\n",
                  'SELECT_FILE_FIRST' => "Por favor selecione um arquivo de importação primeiro<br>\n",
                  'CAN_NOT_ALTER_DOMAINS' => "Alteração de colunas baseadas em domínios não é possível com o Firebird.<br>\n"
                                            ."Ao invés disto mude a definição do domínio na página Acessórios.<br>\n",
                  'CAN_NOT_EDIT_TABLE' => "Não foi possível editar na tabela selecionada.<br>\n"
                                            ."Somente tabelas com um índice de chave primária são editáveis.<br>\n",
                  'CAN_NOT_DEL_TABLE' => "Não foi possível apagar na tabela selecionada.<br>\n"
                                            ."Somente tabelas com um índice de chave primária podem ter linhas apagadas.<br>\n",
                  'DEL_NO_PERMISSON' => "Você não tem permissão para apagar/escrever na tabela %s<br>\n",
                  'EDIT_NO_PERMISSON' => "Você não tem permissão para alterar/escrever na tabela %s<br>\n",
                  'CAN_NOT_ACCESS_DIR' => "O Servidor Web falhou ao acessar o diretório %s<br>\n",
                  'NAME_IS_KEYWORD' => "%s é uma palavra reservada do Firebird<br>\n",
                  'NAMES_ARE_KEYWORDS' => "%s são palavras reservadas do Firebird<br>\n",
                  'NEED_SYSDBA_PW' => "a senha do SYSDBA's é requerida para criar, modificar ou apagar usuários.<br>\n",
                  'PW_REQUIRED' => "A senha é requerida<br>\n",
                  'UN_REQUIRED' => "O nome do usuário é requerido<br>\n",
                  'PW_WRONG_REPEAT' => "A confirmação da senha está incorreta.<br>\n",
                  'BAD_ISQLPATH' => "O executável do isql não está instalado em %s!<br>\n"
                                            ."Por favor verifique o valor para BINPATH em inc/configuration.inc.php.<br>\n",
                  'BAD_TMPPATH' => "Your configured TMPPATH directory '%s' didn't exist or is not writeable for the webserver process!<br>\n"
                                            ."Please check the value for TMPPATH in inc/configuration.inc.php.<br>\n",
                  );

$ERRORS = array('CREATE_DB_FAILED' => 'Criação do Banco de Dados <b>%s</b> falhou!',
                  'NO_DB_SELECTED' => 'Selecione um nome de banco de dados primeiro!<br>',
                  'WRONG_DB_SUFFIX' => 'O nome do banco de dados deve ser terminado com <b>%s</b>',
                  'DB_NOT_ALLOWED' => 'Acesso à <b>%s</b> não permitido.<br>'
                                             .'(verifique $ALLOWED_FILES e $ALLOWED_DIRS em inc/configuration.php)',
                  'NO_IBASE_MODULE' => '<b>Sua instalação do php está sem o suporte à Firebird!</b><br>'
                                            .'Recompile o php e configure --with-interbase[=DIR]<br>'
                                            .'ou modifique o arquivo php.ini para carregar o interbase.so respectivamente interbase.dll.',
                  'DISABLED_CMD' => 'Declarações Sql contendo "%s" são proibidas pela configução!',
                  'BAD_BINPATH' => "Unable to find the command <b>%s</b> !\n"
                                            ."Por favor verifique o valor para BINPATH em inc/configuration.inc.php.\n",
                  );

// charset encoding  for html output
$charset = 'UTF-8';

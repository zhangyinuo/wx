<?php return array(
	'administration tool name test_mail_settings' => 'テストメールの設定',
	'administration tool desc test_mail_settings' => 'この簡単なツールを使用して、Feng Officeのメーラが動作するかどうか確認するためのテストメールを送信できます。',
	'administration tool name mass_mailer' => '一括メール配信',
	'administration tool desc mass_mailer' => 'この簡単なツールを使用して、このシステムに登録されているグループを選択して、その所属ユーザにテキストのメッセージを送信できます。',
	'configuration' => '環境設定',
	'mail transport mail()' => 'デフォルトのPHP設定',
	'mail transport smtp' => 'SMTPサーバ',
	'secure smtp connection no' => 'いいえ',
	'secure smtp connection ssl' => 'SSLを使用',
	'secure smtp connection tls' => 'TLSを使用',
	'file storage file system' => 'ファイルシステム',
	'file storage mysql' => 'データベース(MySQL)',
	'config category name general' => '一般的な設定',
	'config category desc general' => 'Feng Officeの一般的な設定です。',
	'config category name mailing' => 'メールの設定',
	'config category desc mailing' => 'この設定でFeng Officeが使用するメールの送信方法を指定してください。 php.iniの設定のオプションに任せるか、それと異なる他のSMTPサーバを使用するようにここで設定することもできます。',
	'config category name modules' => 'モジュール',
	'config category desc modules' => 'この設定でFeng Officeが使用するモジュールを有効または無効を指定してください。無効に設定するとグラフィカルなインターフェイスからは隠れますが、ユーザに設定した作成・編集等の権限は削除されません。',
	'config category name passwords' => 'パスワード',
	'config category desc passwords' => 'パスワードオプションを管理するためにこれらの設定を使用します。',
	'autentify password title' => '認証パスワード',
	'autentify password desc' => '管理パネルへ入るために必要です。<br/>パスワードを再入力してください',
	'config option name site_name' => 'サイト名',
	'config option desc site_name' => 'この値はダッシュボードページでサイト名として表示されます',
	'config option name file_storage_adapter' => 'ファイルの保管場所',
	'config option desc file_storage_adapter' => 'アップロードしたドキュメントを保存する場所を選択してください。 警告: ストレージを切り替えると、以前にアップロードされたファイルはすべて利用できなくなります。',
	'config option name default_project_folders' => 'デフォルト・フォルダ',
	'config option desc default_project_folders' => 'ワークスペースが作成された時に自動的に作成されるフォルダです。複数のフォルダの名前は、1行に１つずつ記述します。重複した行や、空の行は無視されます。',
	'config option name theme' => 'テーマ',
	'config option desc theme' => 'テーマを使用して、Feng Officeのデザインを変更することができます。',
	'config option name days_on_trash' => 'ゴミ箱に保存する日数',
	'config option desc days_on_trash' => 'ゴミ箱に保存されているオブジェクトを自動的に削除するまでの日数。0を指定した場合は、ゴミ箱から自動的に削除されません。',
	'config option name checkout_notification_dialog' => 'ドキュメントの取り出し時の確認ダイアログ',
	'config option desc checkout_notification_dialog' => 'これをセットすると、ユーザはファイルをダウンロードするときに編集するか読み込み専用か確認を求められます。',
	'config option name time_format_use_24' => '24時間の書式を使用',
	'config option desc time_format_use_24' => 'これをセットすると、12時間の書式の表示に代えて24時間の書式を表示に時刻の使用します。',
	'config option name file_revision_comments_required' => 'ファイルのリビジョンにコメントが必要',
	'config option desc file_revision_comments_required' => 'これをセットすると、新しいファイルのリビジョンを追加するときに、ユーザはそれぞれのリビジョンに新しいコメントを書くことが必要となります。',
	'config option name show_feed_links' => 'フィードのリンクを表示',
	'config option desc show_feed_links' => 'これを使用するとRSSまたはiCalのフィードへのリンクをシステムにログインしたユーザに表示して、ユーザが購読できるようにします。警告: このリンクは、そのユーザでシステムにログインできるかもしれない情報を含みます。このリンクをユーザが不注意に知らせると、そのユーザの情報が漏洩する恐れがあります。',
	'config option name ask_administration_autentification' => '管理作業を認証',
	'config option desc ask_administration_autentification' => '管理パネルにアクセスするときに、パスワードを認証をするダイアログを開くようにします。',
	'config option name enable_notes_module' => 'ノートのモジュールが有効',
	'config option name enable_email_module' => 'メールのモジュールが有効',
	'config option name enable_contacts_module' => '連絡先のモジュールが有効',
	'config option name enable_calendar_module' => 'カレンダーのモジュールが有効',
	'config option name enable_documents_module' => 'ドキュメントのモジュールが有効',
	'config option name enable_tasks_module' => 'タスクのモジュールが有効',
	'config option name enable_weblinks_module' => 'ウェブリンクのモジュールが有効',
	'config option name enable_time_module' => '時間のモジュールが有効',
	'config option name enable_reporting_module' => 'レポートのモジュールが有効',
	'config option name upgrade_check_enabled' => '更新の確認が有効',
	'config option desc upgrade_check_enabled' => '「はい」を選択すると、ダウンロードできるFeng Officeの新しいバージョンの有無をシステムは1日に一度確認します。',
	'config option name work_day_start_time' => '就業日の開始時刻',
	'config option desc work_day_start_time' => '就業日の開始時刻を指定します。',
	'config option name use_minified_resources' => '最小のリソースを使用',
	'config option desc use_minified_resources' => '性能を向上させるために、圧縮されたJavaスクリプトとCSSを使用します。この場合、JavaスクリプトやCSSを変更した場合は、public/toolsを使用して再コンパイルする必要があります。',
	'config option name currency_code' => '通貨',
	'config option desc currency_code' => '通貨記号',
	'user ws config option name detect_mime_type_from_extension' => '拡張子からMIMEタイプを検出する',
	'user ws config option desc detect_mime_type_from_extension' => '拡張仕事にファイルのMIMEタイプを検出するにはこれを有効にします。',
	'config option name exchange_compatible' => 'Microsoft Exchange互換モード',
	'config option desc exchange_compatible' => 'Microsoft Exchange Serverを使用している場合は、このオプションを設定して既知のメール送信の問題を回避してください。',
	'config option name mail_transport' => 'メール配送',
	'config option desc mail_transport' => 'メールの送信は、デフォルトのPHPの設定か、ここで指定したSMTPサーバを使用して行うことができます。',
	'config option name smtp_server' => 'SMTPサーバのホスト、またはIPアドレス',
	'config option name smtp_address' => 'SMTPアドレス',
	'config option desc smtp_address' => 'オプションです。いくつかのサーバはあなたがメールを送るのにそのサーバからのEメールアドレスを使用するのを必要とします。空にするとユーザのメールアドレスを使用します。',
	'config option name smtp_port' => 'SMTPサーバのポート',
	'config option name smtp_authenticate' => 'SMTP認証を使用',
	'config option name smtp_username' => 'SMTP認証のユーザ名',
	'config option name smtp_password' => 'SMTP認証のパスワード',
	'config option name smtp_secure_connection' => 'セキュアなSMTP通信を使用',
	'config option name user_email_fetch_count' => 'メールを取り出す上限',
	'config option desc user_email_fetch_count' => 'ユーザが「メールアカウントの確認」のボタンをクリックしたときに取り出すメールの数です。大きな値を指定すると、ユーザにタイムアウトが起きるかもしれません。0を指定すると無制限となります。この値はcronを通してのメールを取り込みには適用されないことに注意してください。',
	'config option name min_password_length' => 'パスワードの最小の長さ',
	'config option desc min_password_length' => 'パスワードに必要な最小の文字数です。',
	'config option name password_numbers' => 'パスワードに含める数字',
	'config option desc password_numbers' => 'パスワードに含めることが必要な数字の数です。',
	'config option name password_uppercase_characters' => 'パスワードに含める大文字',
	'config option desc password_uppercase_characters' => 'パスワードに含めることが必要な数字の数です。',
	'config option name password_metacharacters' => 'パスワードに含めるメタ文字',
	'config option desc password_metacharacters' => 'パスワードに含めることが必要なメタ文字(例えば . や $ や *)の数です。',
	'config option name password_expiration' => 'パスワードの有効期限(日)',
	'config option desc password_expiration' => '新しいパスワードが有効な日数です。(0にすると、このオプションは無効となります。)',
	'config option name password_expiration_notification' => 'パスワードの有効期限の通知(何日前)',
	'config option desc password_expiration_notification' => 'パスワードの有効期限の前に、ユーザに通知する日数です。(0にすると、このオプションは無効となります。)',
	'config option name account_block' => '有効期限の切れたパスワードのアカウントを停止',
	'config option desc account_block' => 'パスワードの有効期限が切れたときにアカウントを停止します。(ユーザのアカウントを有効にするには管理者権限が必要です。)',
	'config option name new_password_char_difference' => '過去のパスワードと新しいパスワードの文字を確認',
	'config option desc new_password_char_difference' => '新しいパスワードが、ユーザが使用した過去の10個のパスワードと比べて3文字以上の異なる文字を使用しているかどうか確認します。',
	'config option name validate_password_history' => '過去のパスワードと比較',
	'config option desc validate_password_history' => '新しいパスワードが、ユーザが使用した過去の10個のパスワードと一致するかどうか確認します。',
	'config option name checkout_for_editing_online' => 'オンラインの編集時に自動的にチェックアウト',
	'config option desc checkout_for_editing_online' => 'ユーザがドキュメントをオンラインで編集するときにチェックアウトを行い、他のユーザが同時に編集できなくします。',
	'can edit company data' => '会社情報の編集を可能',
	'can manage security' => 'セキュリティの管理を可能',
	'can manage workspaces' => 'ワークスペースの管理を可能',
	'can manage configuration' => '環境設定の管理を可能',
	'can manage contacts' => '連絡先のを管理を可能',
	'can manage reports' => 'レポートの管理を可能',
	'group users' => 'グループのユーザ',
	'user ws config category name dashboard' => 'ダッシュボードのオプション',
	'user ws config category name task panel' => 'タスクのオプション',
	'user ws config category name general' => '一般的なオプション',
	'user ws config category name calendar panel' => 'カレンダーのオプション',
	'user ws config category name mails panel' => '電子メールのオプション',
	'user ws config option name show pending tasks widget' => '未完了のタスクのウィジェットを表示',
	'user ws config option name pending tasks widget assigned to filter' => '割り当てられたタスクのウィジェットを表示',
	'user ws config option name show late tasks and milestones widget' => '遅れているタスクとマイルストーンのウィジェットを表示',
	'user ws config option name show messages widget' => 'ノートのウィジェットを表示',
	'user ws config option name show comments widget' => 'コメントのウィジェットを表示',
	'user ws config option name show documents widget' => 'ドキュメントのウィジェットを表示',
	'user ws config option name show calendar widget' => 'ミニカレンダーのウィジェットを表示',
	'user ws config option name show charts widget' => 'チャートのウィジェットを表示',
	'user ws config option name show emails widget' => 'メールのウィジェットを表示',
	'user ws config option name show dashboard info widget' => 'ワークスペースの説明のウィジェットを表示',
	'user ws config option name show getting started widget' => '「始めましょう」のウィジェットを表示',
	'user ws config option name localization' => 'ローカライズ',
	'user ws config option desc localization' => 'このロケールに応じてラベルや日付は表示されます。設定を反映させるにはページの再読み込みが必要です。',
	'user ws config option name initialWorkspace' => '最初のワークスペース',
	'user ws config option desc initialWorkspace' => 'この設定でログイン直後のワークスペースを、選択しておいたワークスペースにするか、最後に表示していたワークスペースにするか選択できます。',
	'user ws config option name rememberGUIState' => 'ユーザー・インターフェイスの状態を記憶',
	'user ws config option desc rememberGUIState' => 'これを設定すると、グラフィカルインタフェースの状態(パネルのサイズ、広げられたか縮めているかなど)を保存て、次回のログインした時に復元します。警告: この機能はベータ版です。',
	'user ws config option name time_format_use_24' => '時刻の表示に24時間の書式を使用',
	'user ws config option desc time_format_use_24' => 'もし時間表記が有効にした場合には、\'hh:mm\'として 00:00 から 23:59のように表示されます。無効の場合には時間は 1 から 12 のようになり、AM か PMも使用します。',
	'user ws config option name work_day_start_time' => '就業日の開始時間',
	'user ws config option desc work_day_start_time' => '就業日の開始時間を指定します',
	'user ws config option name show workspace widget' => 'ワークスペースの説明ウィジェットを表示',
	'user ws config option name show activity widget' => 'アクティビティウィジェットを表示',
	'user ws config option name my tasks is default view' => '私のタスクをデフォルトビューにします',
	'user ws config option desc my tasks is default view' => '選択しなければ、タスクパネルのデフォルトビューには全てのタスクが表示されます。',
	'user ws config option name show tasks in progress widget' => '「進行中のタスク」のウィジェットを表示',
	'user ws config option name can notify from quick add' => '通知用のチェックボックスの表示',
	'user ws config option desc can notify from quick add' => 'このチェックボックスを選択すると、ユーザにアサインされたタスクが追加または更新されたときに通知されます。',
	'user ws config option name show_tasks_context_help' => 'タスクの状況に応じたヘルプを表示',
	'user ws config option desc show_tasks_context_help' => 'これを有効にすると、タスク・パネルで状況に応じたヘルプ・ボックスが表示されます。',
	'user ws config option name start_monday' => '月曜から週を開始',
	'user ws config option desc start_monday' => 'カレンダーの週の最初を月曜から始めます。(変更を適用するには再表示しなければなりません。)',
	'user ws config option name show_week_numbers' => '週番号を表示',
	'user ws config option desc show_week_numbers' => '月表示、週表示で週番号を表示します。',
	'user ws config option name date_format' => '日付書式',
	'user ws config option desc date_format' => '日付の値に適用する雛形の書式です。d = 日, m = 月, y = 年です。 変更を適用するには再表示しなければなりません。',
	'user ws config option name descriptive_date_format' => '詳しい日付書式',
	'user ws config option desc descriptive_date_format' => '詳しい日付の値に適用する雛形の書式です。コードの説明: d = 日。二桁の数字(先頭にゼロがつく場合も)D = 曜日。3文字のテキスト形式。j = 日。先頭にゼロをつけない。l = 曜日。フルスペル形式。m = 月。数字。先頭にゼロをつける。M = 月。3 文字形式。n = 月。数字。先頭にゼロをつけない。F = 月。フルスペルの文字。Y = 年。4 桁の数字。y = 年。2 桁の数字。再表示が必要です。',
	'user ws config option name show_context_help' => '状況に応じたヘルプ',
	'user ws config option desc show_context_help' => 'ヘルプを常に表示するか、常に表示しないか、それぞれのボックスを閉じる間で表示するか、選択します。',
	'user ws config option name view deleted accounts emails' => '削除したアカウントのメールを表示',
	'user ws config option desc view deleted accounts emails' => '削除したメールアカウントのメールを表示できるようにします。(メールアカウントに対応するメールを削除せずに、メールアカウントを削除したいときに、このオプションを使用します。)',
	'user ws config option name block_email_images' => 'メールの画像を遮断',
	'user ws config option desc block_email_images' => 'メール・オブジェクトに埋め込まれた画像を表示しません。',
	'user ws config option name draft_autosave_timeout' => '下書きの自動保存の間隔',
	'user ws config option desc draft_autosave_timeout' => 'メールの下書きを自動保存する処理の間隔を秒で指定します。(0にすると自動保存を無効にします。)',
	'show context help always' => '常に表示する',
	'show context help never' => '常に表示しない',
	'show context help until close' => '閉じるまで表示する',
	'user ws config option name always show unread mail in dashboard' => 'ダッシュボードに常に未読メールを表示します。',
	'user ws config option desc always show unread mail in dashboard' => '「いいえ」を選択した場合は、アクティブなワークスペースからのメールが表示されます。',
	'workspace emails' => 'ワークスペースのメール',
	'user ws config option name tasksShowWorkspaces' => 'ワークスペースを表示',
	'user ws config option name tasksShowTime' => '時間を表示',
	'user ws config option name tasksShowDates' => '日付を表示',
	'user ws config option name tasksShowTags' => 'タグを表示',
	'user ws config option name tasksGroupBy' => 'グループ',
	'user ws config option name tasksOrderBy' => '順序',
	'user ws config option name task panel status' => 'ステータス',
	'user ws config option name task panel filter' => 'フィルタ',
	'user ws config option name task panel filter value' => 'フィルター値',
	'templates' => 'テンプレート',
	'add template' => 'テンプレートを追加',
	'confirm delete template' => '本当にこのテンプレートを削除しますか?',
	'no templates' => '登録されているテンプレートはありません。',
	'template name required' => 'テンプレート名は必須です。',
	'can manage templates' => 'テンプレートを管理できる。',
	'can manage time' => '時間を管理できる。',
	'can add mail accounts' => 'メールアカウントを追加できる。',
	'new template' => '新しいテンプレート',
	'edit template' => 'テンプレートを編集',
	'template dnx' => '指定したテンプレートは存在しません。',
	'success edit template' => 'テンプレートの更新に成功しました。',
	'log add cotemplates' => '{0}を追加しました。',
	'log edit cotemplates' => '{0}を変更しました。',
	'success delete template' => 'テンプレートの削除に成功しました。',
	'error delete template' => 'テンプレートの削除中にエラー',
	'objects' => 'オブジェクト',
	'objects in template' => 'テンプレート中のオブジェクト',
	'no objects in template' => 'このテンプレートにオブジェクトはありません。',
	'add to a template' => 'テンプレートに追加',
	'add an object to template' => 'このテンプレートにオブジェクトを追加',
	'add a parameter to template' => 'このテンプレートにパラメータを追加',
	'you are adding object to template' => 'この追加する{0}を、以下のテンプレートから選択するか、新しいテンプレートを作成してください。',
	'success add object to template' => 'テンプレートへのオブジェクトの追加に成功しました。',
	'object type not supported' => 'オブジェクトの型はテンプレートをサポートしていません。',
	'assign template to workspace' => 'ワークスペースにテンプレートを割り当てる。',
	'parameters' => 'パラメータ',
	'cron events' => 'Cronイベント',
	'about cron events' => 'Cronイベントについて学ぶ...',
	'cron events info' => 'Cronイベントを使用すると、システムにログインせずにFeng Officeのタスクを定期的に実行させることができます。Cronイベントを有効にするには、Feng Officeのディレクトリ直下にある"cron.php"ファイルを定期的にcronのジョブとして実行するように設定しなければなりません。cronのジョブを実行する周期は、これらのcronのイベントをどの程度の細かさで実行するかを決めることになります。例えば、5分毎にcronのジョブを設定して、アップグレードの有無のチェックを1分毎に確認するようにしたなら、実際のチェックは5分毎に実行されます。cronのジョブを設定する方法について学ぶには、システム管理者かホスティング・サービスの提供者に問い合わせてください。',
	'cron event name check_mail' => 'メールの確認',
	'cron event desc check_mail' => 'このCronイベントはシステムがすべてのメールアカウントの新着メールを確認します。',
	'cron event name purge_trash' => 'ゴミ箱の消去',
	'cron event desc purge_trash' => 'このCronイベントはゴミ箱への保存日数設定で指定された日を超えたオブジェクトを削除します。',
	'cron event name send_reminders' => 'リマシンダーの送信',
	'cron event desc send_reminders' => 'このCronイベントはリマインドメールを送信します。',
	'cron event name check_upgrade' => '更新の確認',
	'cron event desc check_upgrade' => 'このCronイベントはFeng Officeの新しいバージョンを確認します。',
	'cron event name send_notifications_through_cron' => 'Cronを通して通知を送信します',
	'cron event desc send_notifications_through_cron' => 'このイベントが有効な場合、Feng Officeで生成されない時にCronを通してメール通知が送信されます。',
	'next execution' => '次の実行',
	'delay between executions' => '実行の遅延',
	'enabled' => '有効',
	'no cron events to display' => '表示するCronイベントはありません。',
	'success update cron events' => 'Cronイベントの更新に成功しました。',
	'manual upgrade' => '手動更新',
	'manual upgrade desc' => 'Feng Officeの手動更新はFeng Officeの最新バージョンをダウンロードし、インストールするルートにそのファイルを展開してから、ブラウザを使用して <a href="public/upgrade">\'public/upgrade\'</a>を実行します。',
	'automatic upgrade' => '自動更新',
	'automatic upgrade desc' => '自動更新は、自動的に新しいバージョンのダウンロードとファイルの展開を行い、アップグレードの処理を実行します。ウェブサーバはすべてのフォルダに書き込みアクセスの権限がなければなりません。',
	'start automatic upgrade' => '自動更新を開始',
	'select object type' => 'オブジェクトの型を選択',
	'select one' => '-- 選択してください --',
	'email type' => 'メール',
	'custom properties updated' => '更新されたカスタム・プロパティ',
	'user ws config option name noOfTasks' => 'デフォルトで表示するタスクの数を設定',
	'user ws config option name amount_objects_to_show' => 'リンクされたオブジェクトの表示数',
	'user ws config option desc amount_objects_to_show' => 'オブジェクトの表示に一覧する、リンクされたオブジェクトの個数を設定します。',
	'user ws config option name show_two_weeks_calendar' => '2週間のカレンダーのウィジェットを表示',
	'user ws config option desc show_two_weeks_calendar' => '2週間のカレンダーのウィジェットの表示を指定します。',
	'user ws config option name attach_docs_content' => 'ファイルの内容を添付',
	'user ws config option desc attach_docs_content' => 'このオプションを「はい」にすると通常のメールにファイルを添付します。「いいえ」にするとファイルへのリンクとして送信します。',
	'user ws config option name max_spam_level' => '許可する最大スパムレベル',
	'user ws config option desc max_spam_level' => 'When fetching emails, messages with Spam evaluation greater than this value will be sent to "Junk" folder. Set to 0 for max filtering, 10 no filtering. This option works only if a spam filter tool is installed in your server.',
	'user ws config option name hide_quoted_text_in_emails' => 'メール閲覧時に引用文を隠す',
	'user ws config option desc hide_quoted_text_in_emails' => 'If enabled email messages will be displayed without the quoted text. There will be an option to view it while reading.',
	'edit default user preferences' => 'デフォルトのユーザ設定を編集',
	'default user preferences' => 'デフォルトのユーザ設定',
	'default user preferences desc' => 'ユーザ設定のデフォルトの値を選択します。この値は、ユーザがオプションを選択していない場合の値となります。',
	'mail accounts' => 'メールアカウント',
	'incoming server' => '入ってくるサーバ(Incoming)',
	'outgoing server' => '出て行くサーバ(Outgoing)',
	'no email accounts' => 'メールアカウントはありません',
	'user ws config option name create_contacts_from_email_recipients' => 'Create contacts from email recipients',
	'user ws config option desc create_contacts_from_email_recipients' => 'When this option is set to "Yes" a contact will be automatically created for every email address you send an email to. You need the "Can manage all contacts" permission.',
	'user ws config option name drag_drop_prompt' => 'Action to take on drag and drop to workspace',
	'user ws config option desc drag_drop_prompt' => 'Choose which action should be taken when dragging an object to a workspace.',
	'drag drop prompt option' => 'Prompt user for an action',
	'drag drop move option' => 'Move to new workspace and lose previous workspaces',
	'drag drop keep option' => 'Add to new workspace while keeping previous workspaces',
	'user ws config option name mail_drag_drop_prompt' => 'Classify email attachments on drag and drop?',
	'user ws config option desc mail_drag_drop_prompt' => 'Choose what should be done with email attachments when dragging an email to a workspace.',
	'mail drag drop prompt option' => 'Prompt user for an action',
	'mail drag drop classify option' => 'Classify attachments',
	'mail drag drop dont option' => 'Don\'t classify attachments',
	'user ws config option name show_emails_as_conversations' => 'Show emails as conversations',
	'user ws config option desc show_emails_as_conversations' => 'If enabled email will be grouped into conversations in the Emails listing, showing all emails belonging to a same thread (replies, forwards, etc) as one entry in the listing.',
	'user ws config option name autodetect_time_zone' => 'Autodetect timezone',
	'user ws config option desc autodetect_time_zone' => 'When this option is enabled, the user\'s timezone will be autodetected from browser.',
	'user ws config option name search_engine' => 'Search engine',
	'user ws config option desc search_engine' => 'Choose which search engine to use. "Full" will do a more exhaustive search but will take much longer than "Quick".',
	'user ws config option name activity widget elements' => 'Activity widget size',
	'user ws config option desc activity widget elements' => 'Number of items displayed in Activity widget.',
	'search engine mysql like' => 'Full',
	'search engine mysql match' => 'Quick',
	'user ws config option name task_display_limit' => 'Maximum number of tasks to display',
	'user ws config option desc task_display_limit' => 'For performance reasons, this number should not be too big. Use 0 for no limit.',
	'config option name use_owner_company_logo_at_header' => 'アプリケーションのロゴとして所有会社のロゴを使用',
	'config option desc use_owner_company_logo_at_header' => '画面の右上の隅に所有会社のロゴを表示します。変更を適用するには再表示(F5)しなければなりません。推奨する大きさ: 50x50 px',
	'config option name use_time_in_task_dates' => 'タスクの日付に時刻を使用',
	'config option desc use_time_in_task_dates' => '開始日と期日に開始時刻と期限の時刻を入力できるようにします。',
	'config option name untitled_notes' => '無題のノート',
	'config option desc untitled_notes' => '題名のないノートを作成できるようにします。',
	'config option name repeating_task' => '繰り返すタスク',
	'config option desc repeating_task' => '繰り返すタスクを単一のタスクではなく、個別のタスクとして表示しします。',
	'config option name working_days' => '就業日',
	'config option desc working_days' => '会社の就業日を選択出来ます。カレンダーと繰り返すタスクで役に立ちます。',
	'config option name wysiwyg_messages' => 'メッセージの説明をWYSIWYG',
	'config option desc wysiwyg_messages' => 'このオプションを有効にすると、ノートの説明をリッチテキスト形式で書けます。',
	'config option name wysiwyg_tasks' => 'タスクの説明をWYSIWYG',
	'config option desc wysiwyg_tasks' => 'このオプションを有効にすると、タスクの説明をリッチテキスト形式で書けます。',
	'config option name wysiwyg_projects' => 'プロジェクトの説明をWYSIWYG',
	'config option desc wysiwyg_projects' => 'このオプションを有効にすると、プロジェクトの説明をリッチテキスト形式で書けます。',
	'config option name check_spam_in_subject' => '件名のspamを確認',
	'config option name show images in document notifications' => 'ドキュメントの通知に画像を添付',
	'config option desc show images in document notifications' => 'ドキュメントが画像の場合に、通知の内容に添付します。',
	'config option name infinite_paging' => 'ページ分割',
	'config option desc infinite_paging' => 'ページ分割を有効または無効にします。有効にすると、一覧を読み込むときにわずかな性能の向上がわかるかもしれません。',
	'config option name block_login_after_x_tries' => '5回のログインの失敗の後でブロック',
	'config option desc block_login_after_x_tries' => 'ユーザーが10分以内に5回続けてログインに失敗すると、アカウントを10分の間ブロックします。',
	'config option name use_milestones' => 'マイルストーンを使用',
	'config option name show_tab_icons' => 'タブにアイコンを表示',
	'timeslot' => '時間',
	'module permissions' => 'モジュールの権限',
	'config option desc use_milestones' => 'このオプションはマイルストーンとそれに関連するタスクを作成できるようにします。',
	'config option desc show_tab_icons' => 'このオプションはそれぞれのタブにアイコンを表示します。',
	'user ws config category name listing preferences' => 'リストのオプション',
	'config option name automatic_crpm_status_calculation' => '顧客とプロジェクトの自動状態',
	'config category name documents' => 'ファイル',
	'config category desc documents' => 'ファイルの構成を管理します。',
	'user config option name sendEmailNotification' => '電子メールで通知を送信',
	'user config option desc sendEmailNotification' => 'これを設定すると、作成したユーザーに通知の電子メールを送信します。',
	'owned by' => '所有者',
	'config option name can_assign_tasks_to_companies' => 'タスクを会社に割り当て可能',
	'config option desc can_assign_tasks_to_companies' => 'これを有効にすると、タスクを追加や変更するときに「割り当て先」の選択肢を表示します。',
	'config option name use_object_properties' => 'オブジェクトの属性を使用',
	'config option desc use_object_properties' => 'このオプションを有効にすると、それぞれのオブジェクトに独自の属性(キーと値)を設定できます。',
); ?>

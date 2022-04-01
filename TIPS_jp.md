https://www.udemy.com/course/build-a-youtube-clone/  
を始める前に、Laravelをインストールするだけでなく、Vueも  
https://levelup.gitconnected.com/how-to-set-up-and-use-vue-in-your-laravel-8-app-2dd0f174e1f8  
のようにインストールする。  
ただし、4. の"npm install & npm run dev"まで。  
  
  
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013230?start=30#overview  
の1:47は  

>    php artisan make:auth

に対応するが、必要ない。  
何故ならば、  

>    composer require laravel/ui
>    php artisan ui vue --auth
>    php artisan migrate
>    composer update

https://stackoverflow.com/questions/34545641/php-artisan-makeauth-command-is-not-defined/34546836#34546836  
を代わりに実行すればよい。  

# User.php

https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013234#overview
の0:38  
User.phpは\\\\app\\\\Models\\\\User.php  
  
# ~~php artisan app:name→やる必要なし、余計なエラーの元~~
~~https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013234#overview~~  
~~の0:46~~  
~~新しいバージョンのLaravelではこのコマンドが廃止されてしまった。~~  
~~そこで、新たにコマンドを作る：~~  
~~https://gist.github.com/isluewell/b824c0aef32f5007170fcd0d8498b657~~  
  
  
# \\app\\Http\\Controllers\\ChannelController.php
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013246#overview
1:53で、ファイルの冒頭に  
  
>    use App\\Models\\Channel;
  
を追加。また  
\\app\\Providers\\RouteServiceProvider.phpの  

>        // protected $namespace = 'App\\\\Http\\\\Controllers';
  
のコメントを外す。  


# npm run watch
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013246?start=390#overview
の6:28。  
このコマンドは新規にターミナルを開いて実行した方が良い。というのも、このコマンドは常駐してソースコードに変更があるごとにコンパイルし直す仕様なので、コマンドを実行した後に終了してターミナルの入力画面に戻ってこないから。  
  
# 既存のDatabaseのテーブルを一旦削除したいとき
例えば、テーブルAのmigrationファイルを新しいタイムスタンプで作ったとき。新しいmigrationファイルでoho artisan migrateを実行すると、  
>   Illuminate\\Database\\QueryException
>  SQLSTATE[42P07]: Duplicate table: 7 ERROR:  relation "A" already exists  


とエラーになる。この場合は一旦テーブルを消して作り直さないといけない。それをするには新しいmigrationファイルで、
>class CreateATable extends Migration
>{
>    public function up()
>    {
>        Schema::dropIfExists("A");
>        Schema::create('A', function (Blueprint $table) {
>            $table->bigIncrements('id');

のようにSchema::dropIfExist("A");を加える。  
また、Aに紐づいたpackageがある場合にはそれも削除する。今の場合はA=mediaでspatie/laravel-medialibraryが紐づいてるから、それを削除する：  
>composer remove spatie/laravel-medialibrary

その後インストールし直し、上で加えた行を削除。  
しかし、spatie/laravel-medialibraryの場合は次の固有の場合に帰着。  

# spatie/laravel-medialibraryのmigration
少なくとも9.11.1バージョンではmigrationファイルに  
>    public function down()
>    {
>        Schema::dropIfExists('media');
>    }

を加えないといけない。これが無いとphp artisan migrate:refreshしたときに一旦テーブルが削除されないまま続行するので前項のエラーが出る。  
https://github.com/spatie/laravel-medialibrary/issues/2534  

# HasMediaTrait
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013248#search
の3:12で  
HasMediaTraitをInteractsWithMediaに置き換え。Laravel-MediaLibraryのversion 7から8での変更。  
https://github.com/spatie/laravel-medialibrary/blob/main/UPGRADING.md

# Mediaクラスをuuid仕様にする
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013248#search
の2:18で、primaryKeyをidを整数のincrementsにしている。  
しかし、これで実行すると  
>SQLSTATE[22P02]: Invalid text representation: 7 ERROR: invalid input syntax for type bigint: "uuidの文字列"
とエラーが出る。どうも、idに文字列(uuid)は代入できない、と怒られているらしい。udemyのビデオではidは弄らなくていいと言っているが、どうも僕の環境では変える必要がありそうだ。(その理由が、新バージョンだからなのかPostgreSQLだからなのかは不明。)　以下のようにする。  
1. database\\migrationsのmigrationファイル  
>    public function up()
>    {
>        Schema::create('media', function (Blueprint $table) {
>***            $table->uuid('uuid')->unique()->primary();  ***
>            // $table->bigIncrements('id');
>            // $table->morphs('model');
>***            $table->string("model_type");  ***
>***            $table->uuid("model_id");  ***
>            // $table->uuid('uuid')->nullable()->unique();
>            $table->string('collection_name');
>            $table->string('name');
  
2. models\\Media.phpを新規作成  
><?php
>namespace App\\Models;
>
>use Spatie\\MediaLibrary\\MediaCollections\\Models\\Media as BaseMedia;
>
>class Media extends BaseMedia {
>    protected $primaryKey = "uuid";
>    protected $keyType = "string";
>    public $incremanting = false;
>}

3. config\\media-library.phpを変更  
  
>    'media_model' => App\\Models\\Media::class,
  
参考：  
https://stackoverflow.com/questions/62171634/spatie-laravel-medialibrary-change-primary-key/62171867#62171867
  
# Tinkerの使い方
ゲスト側から  
>php artisan tinker
例えば、  
>\\User::first()->load('channel');
のように実行する。  
  
storage/app/public以下のファイル(サムネイル)をブラウザで開こうとすると404 Not Foundになる  
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013248#search
の10:25。ホスト側のコマンドラインを管理者権限で立ち上げて、プロジェクトフォルダに移動。そして、  
>php artisan storage:link
を実行。その後にブラウザを再読み込みとかすると表示されるようになる。  
https://qiita.com/shioharu_/items/608d024c48d9d9b5604f  

# サムネイルの画像がずれる
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013248#search
の11:33。  
原因は、\\public\\css\\app.cssの665行が  
>  --bs-gutter-x: 1.5rem;
のため。これを  
>  --bs-gutter-x: 0rem;
に変える。  
  
# Vue DevTools
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013256#search
の2:25。ブラウザがMicrosoft EdgeでもChrome版のVue DevToolsをインストール。Developer tools (F12)でVueタブが出て来ないときには一旦ブラウザを閉じて再起動。  

# "laratube/js/bootstrap.esm.js.map"がない
というエラーがブラウザのコンソールで出るが、  
https://github.com/twbs/bootstrap/blob/main/dist/js/bootstrap.esm.js.map
をダウンロードして、  
/public/jsに置く。  

# !!の使い方
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013256#search
の3:57。
>"{{ }}"
を文字化けしないように
>'{!! !!}'
とする。シングルクォーテーションが必要。  
  
  
# resources/js/app.js
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013256#questions/7748204
の1:14で、  
>window.Vue = require('vue').default;
の必ず後に  
>require("./components/subscribe-button").default;
を置く。何故ならば、subscribe-button.jsの中で変数Vueを使うから。  
あといつの段階か分からないが、どこかの段階でVuejsでは.defaultを付けないといけないようだ。  
https://stackoverflow.com/questions/49138501/vue-warn-failed-to-mount-component-template-or-render-function-not-defined-i/49139332#49139332

# Laravel 8でのfactory
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013258#questions/7748204
の0:28。  
>$factory->define(...
や
>protected $model = ...
は必要ない。  
https://www.amitmerchant.com/class-based-model-factories-in-laravel-8/
https://stackoverflow.com/questions/70310252/why-is-php-artisan-makefactory-not-generating-with-a-model/70310528#70310528
https://github.com/laravel/framework/pull/39310
最新バージョン(8.82.0)では  
>    public function definition()
>    {
>        return [
>            "name" => $this->faker->sentence(3),
>            "user_id" => function() {
>                return User::factory()->create()->id;
>            },
>            "description" => $this->faker->sentence(30),
>        ];
>    }
  
が正しい。ここではdefinition()の実装だけでなく、
>factory(User::class)->create()

が、
>User::factory()->create()

になることにも注意。  
https://qiita.com/nunulk/items/37cfe88b5fa999c71ea1
  
また、  
Factoriesフォルダ内のファイルは、Channel.phpなどではなくChannelFactory.phpと自動的になる。  

https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013258#questions/7748204
の4:58では、  
>factory(Subscription::class, 10000)->create()
ではなく、
>Subscription::factory()->count(10000)->create()
となる。  
https://laravel.com/docs/8.x/database-testing#instantiating-models


# LaravelとVuejsのネーミング
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013274?components=available_coupons%2Cbuy_button%2Cbuy_for_team%2Ccacheable_add_to_cart%2Ccacheable_buy_button%2Ccacheable_deal_badge%2Ccacheable_discount_expiration%2Ccacheable_price_text%2Ccacheable_purchase_text%2Ccurated_for_ufb_notice_context%2Cdeal_badge%2Cdiscount_expiration%2Cgift_this_course%2Cincentives_context%2Cinstructor_links%2Clifetime_access_context%2Cmoney_back_guarantee%2Cprice_text%2Cpurchase_tabs_context%2Cpurchase%2Crecommendation%2Credeem_coupon%2Csidebar_container%2Csubscribe_team_modal_context#announcements
の0:37。  
Javascriptの方ではinitialSubscriptionsでcamelCase,一方bladeではinitial-subscriptionsとkebab-case。  
https://medium.com/js-dojo/properly-passing-data-from-laravel-blade-to-vue-components-57689b43d7fc

そうしないと次のエラーがブラウザで出る。
>[Vue tip]: Prop "initialsubscriptions" is passed to component <Anonymous>, but the declared prop name is "initialSubscriptions". Note that HTML attributes are case-insensitive and camelCased props need to use their kebab-case equivalents when using in-DOM templates. You should probably use "initial-subscriptions" instead of "initialSubscriptions"
>
>[Vue warn]: Missing required prop: "initialSubscriptions"


# 動画がアップロードできない：413 (Request Entity Too Large)
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013292#questions/8233850
の10:23。  

ゲスト側から、nginxの設定ファイル/etc/nginx/nginx.congに、  
>http {
>	upload_max_body: 100M;
>}
として、  

>sudo -s systemctl restart nginx

を実行。  

https://stackoverflow.com/questions/58811513/413-request-entity-too-large-nginx-server-in-laravel-homestead-for-windows/58812002#58812002

# Laravel 8 Installation -> Monolog 1.3 error
https://github.com/PHP-FFMpeg/PHP-FFMpeg/issues/823#issuecomment-887729341
composer.lockを消して、
composer require pbmedia/laravel-ffmpegを実行。
https://github.com/PHP-FFMpeg/PHP-FFMpeg/pull/849
  
  
# Unable to load FFMpeg
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013310?components=available_coupons%2Cbuy_button%2Cbuy_for_team%2Ccacheable_add_to_cart%2Ccacheable_buy_button%2Ccacheable_deal_badge%2Ccacheable_discount_expiration%2Ccacheable_price_text%2Ccacheable_purchase_text%2Ccurated_for_ufb_notice_context%2Cdeal_badge%2Cdiscount_expiration%2Cgift_this_course%2Cincentives%2Cinstructor_links%2Clifetime_access_context%2Cmoney_back_guarantee%2Cprice_text%2Cpurchase_tabs_context%2Cpurchase%2Crecommendation%2Credeem_coupon%2Csidebar_container%2Csubscribe_team_modal_context#questions
の4:38。  
  
>[2022-02-12 09:20:42] local.ERROR: Unable to load FFMpeg {"exception":"[object] (FFMpeg\\\\Exception\\\\ExecutableNotFoundException(code: 0): Unable to load FFMpeg at /home/vagrant/code/......./laratube/vendor/php-ffmpeg/php-ffmpeg/src/FFMpeg/Driver/FFMpegDriver.php:55)
  
これは、ゲスト側にffmpegが入ってないから。
>sudo -s apt-get install ffmpeg
で解決。  
  
https://tutorialmeta.com/question/installing-ffmpeg-in-lumen
  
  
# 動画アップロードのコードを変えたときに反映されない
大抵は、
>php artisan queue:work --sleep=0 --timeout 60000
をゲスト側で一旦CTRL+Cで止めて再実行すればいい。  


# Thumbs-upとthumbs-downのsvgの内容
https://github.com/bahdcoder/build-a-youtube-clone-in-laravel-and-vuejs/blob/341e0feab001ebbbfceab3f4c0f0ebf350866a65/resources/js/components/votes.vue
の0:29。  
githubのbahdcoder / build-a-youtube-clone-in-laravel-and-vuejsの、resources/js/components/votes.vueにある。具体的なリンクは、  
https://github.com/bahdcoder/build-a-youtube-clone-in-laravel-and-vuejs/blob/341e0feab001ebbbfceab3f4c0f0ebf350866a65/resources/js/components/votes.vue  
また、このままだと大きすぎるので同じくvideo.blade.phpの<style>以下に、
>.thumbs-up, .thumbs-down {
>	width: 20px;
>}
を追加。というか、動画の0:00を見ると指定するstyleが全部載っている。
  
  
# Laravel 8でのstr_pluralはStr::plural
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013370?components=add_to_cart%2Cavailable_coupons%2Cbuy_button%2Cbuy_for_team%2Ccacheable_buy_button%2Ccacheable_deal_badge%2Ccacheable_discount_expiration%2Ccacheable_price_text%2Ccacheable_purchase_text%2Ccurated_for_ufb_notice_context%2Cdeal_badge%2Cdiscount_expiration%2Cgift_this_course%2Cincentives%2Cinstructor_links%2Clifetime_access_context%2Cmoney_back_guarantee%2Cprice_text%2Cpurchase_tabs_context%2Cpurchase%2Crecommendation%2Credeem_coupon%2Csidebar_container%2Cpurchase_body_container#questions
の1:44。  

# .vue拡張子のファイルについて
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013370?components=add_to_cart%2Cavailable_coupons%2Cbuy_button%2Cbuy_for_team%2Ccacheable_buy_button%2Ccacheable_deal_badge%2Ccacheable_discount_expiration%2Ccacheable_price_text%2Ccacheable_purchase_text%2Ccurated_for_ufb_notice_context%2Cdeal_badge%2Cdiscount_expiration%2Cgift_this_course%2Cincentives%2Cinstructor_links%2Clifetime_access_context%2Cmoney_back_guarantee%2Cprice_text%2Cpurchase_tabs_context%2Cpurchase%2Crecommendation%2Credeem_coupon%2Csidebar_container%2Cpurchase_body_container#questions
の4:48。  
VSCodeでvueファイルにコードを書きこむとintellisenseエラーがたくさん出る。例えば、  
>JSX expressions must have one parent element
本質的なエラーではなく、VSCodeにvue用のエクステンションが入ってないのが原因。  
例えば、Veturをインストールする。  
https://code.visualstudio.com/docs/nodejs/vuejs-tutorial


# text-rightが機能しない  
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013372#questions
の6:14。  
新バージョンのbootstrapではtext-endとなる。  
https://stackoverflow.com/questions/38746394/text-center-and-text-right-not-working-in-bootstrap/66616885#66616885


# textareaに入力したあとにsubmitすると冒頭に余計な余白が入る
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013372#questions
の6:18。
>                            <textarea name="description" cols="3" rows="3" class="form-control">
>                                {{ $video->description }}
>			    </textarea>
のように{{ $video->description }}の前に余計な余白を入れてはいけない。正しくは、
>                            <textarea name="description" cols="3" rows="3" class="form-control">{{ $video->description }}</textarea>
のように1行で記述。  
  
# GitHubのHTMLファイルを開く
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013394#search
の9:37。  
https://htmlpreview.github.io
から  
https://github.com/eliep/vue-avatar/blob/master/docs/index.html
を開く。  

# d-flexでvue-avatarの横幅が縮む
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013394#search
の11:36。  
avatarの後を改行しないためにd-flexキーワードを付けたが、副作用としてvue-avatarの縦横1:1にならない。横が縮む。  
そこで、
>        <div class="media d-flex" v-for="comment in comments.data">
>            <avatar :username="comment.user.name" :size="30"></avatar>
を
>        <div class="media d-flex" v-for="comment in comments.data">
>            <div>
>                <avatar :username="comment.user.name" :size="30"></avatar>
>            </div>
とすると上手くいく。  
  
  
# axiosの.then({data})について
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013398#search
の2:00あたり。  
もし、.then(res)ならばresの変数名は任意。ただし、{data}は返り値のなかのdataキーワードを拾うという意味。dataはaxiosの中で決められている予約語なので、他の名前にしてはいけない：  
https://github.com/axios/axios#response-schema

# bootstrapのtext-centerで中心揃えにならない
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013402#search
の4:07。Load Repliesが中心揃えになるべきが実際はそうはならない。  
Element Inspectionで見ると、親コンテナがwidthが固定されてないのが原因。  
(w-fullだと後に付けるコメントのいいね・悪いねボタンの幅が確保できないのでw-80を採用したくなるが、結局コメントの左下に付けるので幅の心配はしなくていいのでw-full。)  
そこで、<repilies></replies>が入っているcomponent.vueの  
  
>            <div class="media-body" style="margin-left: 10px;">
>                <h6 class="mt-0">{{ comment.user.name }}</h6>
>                <small>
>                    {{ comment.body }}
>                </small>
>                <replies></replies>
>            </div>

を
>            <div class="media-body w-full" style="margin-left: 10px;">
>                <h6 class="mt-0">{{ comment.user.name }}</h6>
>                <small>
>                    {{ comment.body }}
>                </small>
>                <replies></replies>
>            </div>

とする。  
  
# 2番目のコメントに返信を追加するときのエラー
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013402#search
の17:27。  
>factory(Comment::class, 50)->create(["comment_id"=>"f1d63a98-cebd-4b8c-8aff-1eea85b999e2"]);

は、
>Comment::factory()->count(50)->create(
>['parent_comment_id'=>'f1d63a98-cebd-4b8c-8aff-1eea85b999e2']);

となる。  
  
  
# コメントのReplyのCancelボタンが左隣のいいね・よくないねと重なる
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013410#search
の4:59。  
><button @click="addingReply = !addingReply" class="btn btn-sm mr-2" ...>

を
><button @click="addingReply = !addingReply" class="btn btn-sm mx-2" ...>

にする。  

# コメントのReplyが最新順にならない
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013414#search
の5:33。  
これは、階層最上位の->orderBy("created_at", "DESC");を入れる。  
Comment.phpのreplies()関数で、  
>return $this->hasMany(Comment::class, "parent_comment_id")->whereNotNull("parent_comment_id")->orderBy("created_at", "DESC");
を追加。  

# Paginationでページ数の数字が表示されず、PreviousとNextが表示される。
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013418#search
の8:51。  
まず、Laravel 8から何故かpaginatorがデフォルトでbootstrapからtailwindを使うように変更になったので、デザインが異なる。bootstrapを使うようにするには。app\AppServiceProvider.phpのboot関数を、  
>    public function boot()
>    {
>        Paginator::useBootstrap();
>    }

とする。  
https://laravel.com/docs/8.x/pagination#using-bootstrap  
  
# Paginationが左寄せになって中心揃えにならない  
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013418#search
の8:51。  
classの設定のrowをd-flexに変える
>    <div class="d-flex justify-content-center" style="text-align: center;">
>        {!! $videos->links() !!}
>    </div>

https://note.com/kakidamediary/n/nc7208b587d0c  
  
  
# Web.phpのRoute::resourceについて
https://www.udemy.com/course/build-a-youtube-clone/learn/lecture/15013422#reviews
の6:17。  
Route::resourceを指定するだけで一通りのroute(show、index、createなど)が定義される。  
https://www.geeksforgeeks.org/laravel-routeresource-vs-routecontroller/




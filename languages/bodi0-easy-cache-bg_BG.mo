��    T      �  q   \         &   !     H     U     b     p  $        �     �     �     �     �          ,     F  %   ^  A   �  '   �  C   �  
   2	     =	     Y	      h	  #   �	     �	     �	  @   �	  e   
  	   w
  l  �
  �   �     �  0  �  Z   �              #     B     b          �     �  :   �     �     �       
   	       /        J  �   ]     �     �       J       i  >  �  �   �  G   �    �  J   �  
   )  �   4  �   4       �     v   �     (  �   5  K   �  #        5  2   T  9   �  N   �  �        �     �     �     �     �     �     �  
   �  �  �  \   v     �     �       "     3   >     r  ,   �  7   �  L   �  J   ?   &   �   3   �   ;   �   =   !!  w   _!  P   �!  �   ("  
   �"  '   �"  ,   �"  Y   #  O   e#  &   �#     �#  �   �#  �   ~$  
   H%  T  S%    �'     �(  z  �(  �   C,  �  �,     �.  ;   �.  2   �.  ;   ,/  /   h/     �/     �/     �/  )   10  /   [0     �0     �0     �0  �   �0  4   K1  F  �1     �2  +   �2  .    3  �   /3  @  �3  �  +6  �  9  �   �:  A  #;  p   e=     �=  �  �=  �  �?  4   �A  =  �A  �   C     �C  ;  �C  �   :E  >   �E  :   *F  _   eF  S   �F  �   G  �   �G     �H     �H     �H     �H     �H     �H  	   �H     �H             *       L   %   T   I         7   
          S              3   B                   :   .   ,   ?   5          9   6         N   F      M           G   (                 >   A   !   C                 1   K                     H   "   @      &      	   D   8   =   0   '             /       R             <      -       4   E   +   Q                  P   ;      J   O   $   2       #       )                        cannot be found or cannot be deleted.  created on   was deleted  were deleted  were restored 1 hour=60, 1 day=1440, 1 week=10080. Administration All cached files  Average Web Server load Cache files age (min/max/avg) Cache files size (min/max/avg) Cache folder path Cached file expires after Delete all cached files Delete combined and minified CSS file Details about size of minified and combined CSS file, in folder:  Displays freshness of the cached files. Displays smallest, largest and averaged size of saved cached files. Easy cache Easy cache [Administration] Enable caching Exclude pages/posts from caching Exclude search queries from caching Files are inside cached folder GiB If you find this plugin useful, I wont mind if you buy me a beer If you want to re-create it, fill the corresponding textarea with URL of CSS files and save settings. Important Insert absolute URL (valid URL according RFC 2396) of CSS files in sequence of their appearance in non-cached page for minification and combination. This process will reduce the number and size of HTTP requests to your server. The CSS files will be merged as single cached CSS resource file, named<code>_css.min.css</code> and saved in your current theme's folder. Keep in mind, that generated cached files *may* include the currently deleted CSS file, you have to re-build those files or re-create the CSS file. KiB Make sure that any URL inside the original CSS code is abosulte, not relative (otherwise you will have missing backrounds).  Also make sure you type the URL of files you want to combine and minify exactly as it is in your original page / post (for example some stylesheets links may have dynamic content attached to them, like: <u>http://www.example.com/color.php?ver=1.2</u>), otherwise minification and combination will not work correctly. If you modify the original CSS files, remember to save settings here in order to re-generate cached CSS resource file. Make sure that folder path exists and is writable, i.e. the permissions are 755 or higher. Make sure the call to <code>wp_footer();</code> function is at the very bottom in your theme`s <code>footer.php</code> file, right before closing <code>< /body></code> tag. Otherwise contents after <code>wp_footer();</code> may be not included in generated cache file. MiB Minified and combined CSS file Minified and combined CSS file  Minify and combine CSS files Minify saved cache file N/A No No cache files were found or cache files cannot be deleted Not Available Number of cache files found Pages Parameters Posts Rebuild cached file on page/post/comment update Refresh statistics Remember to logout or use different browser if you want to test if caching mechanism is working, only not logged-in users can benefit from caching. Reset Restore default settings Save settings Search queries or individual pages and posts can be excluded from caching. Select pages or posts from published ones, which you don`t want to be cached, sorted by title, private pages or posts are also in this list. Excluding posts/pages is useful when you have registration form or any other specific (custom) search form implemented on these posts/pages. Select to automatically recreate cached file on disk when post or page has been modified (including when a comment is added or updated, which causes the comment count for the page/post to update), useful if you do not want your website visitors to wait for cache to expire to get the latest page/post/comments changes. Select to cache or not all search results in your web site. It will be useful to set this option to 'No' if you have huge amount of traffic, generated by searches in order to speed-up page/post display. Select to enable or disable caching mechanism globally in your website. Select to optimize saved cache file on disk for further performance improvement by striping extra spaces, new lines, tabs, etc., on average minification reduces the cache file size within 6 to 12%, depending on how formatted is the HTML content of non-cached file. Speed up your website by setting the parameters for the caching mechanism. Statistics The <code>allow_url_fopen</code> variable is set to <code>Off</code> in your <code>php.ini</code> configuration, no CSS minification and combination is possible. Contact your hosting company or web server administrator for details how this can be changed. The cached files are automatically  updated/deleted when pages/posts are updated (including when a comment is added or updated, which causes the comment count for the post to update) or deleted permanently. The default settings  The default value is sub-folder, named < cached > inside the default WordPress 'uploads' folder. Leave this field empty for restoring the default path. The plug-in creates in selected cache folder cache file for every requested page, according to the caching parameters. The settings These values represents the current system software, average system load in the last 1, 5 and 15 minutes, also memory usage (current and peak). This file will be included in every cached file, old links will be removed. Total space occupied by cache files URL of CSS files, one per line Value in minutes, integers only, greater than zero Values above 80 means that your web server is overloaded. Warning: Caching is disabled, your blog will display non-cached pages to users Warning: Selected cache folder path cannot be created, it is not writable, choose another one, otherwise your blog will not function properly Yes bytes d h min sec w were saved Project-Id-Version: bodi0 easy cache
POT-Creation-Date: 2014-02-28 12:50+0200
PO-Revision-Date: 2014-02-28 12:50+0200
Last-Translator: Budyoni Damyanov <budiony@gmail.com>
Language-Team: 
Language: bg
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Generator: Poedit 1.6.3
X-Poedit-Basepath: .
Plural-Forms: nplurals=2; plural=(n != 1);
X-Poedit-KeywordsList: __;_e
X-Poedit-SearchPath-0: ..
  не може да бъде намерен или не може да бъде изтрит.  създаден на   беше изтрит  бяха изтрити  бяха възстановени 1 час=60, 1 ден=1440, 1 седмица=10080. Администрация Всички кеширани файлове Средно натоварване на сървъра Възраст на кеш файловете (мин/макс/средна) Размер на кеш файловете (мин/макс/среден) Път до папката с кеша Кешираният файл изтича след Изтриване на кешираните файлове Изтриване на комбинирания CSS файл Детайли за размера на минимизирания и комбиниран CSS файл, в папка: Показва колко пресни са кешираните файлове. Показва най-малкия, най-големия и средния размер на кешираните файлове. Easy cache Easy cache [Администрация] Включване на кеширането Изключване на страници/публикации от кеширането Изключване на търсещите заявки от кеширане Файловете са в папка  GiB Ако смятате, че този плъгин ви е бил полезен, не бих имал против ако ме почерпите с бира Ако искате да го създадете отново, попълнете прилежащото текстово поле с URL на CSS файла и запазете настройките. Важно Въведете абсолютен URL (по документацията RFC 2396) на CSS файловете в последователност както при некешираната страница за обединяване и минимизиране. Този процес ще намали броя и размера на HTTP заявките към вашия сървър. CSS файловете ще бъдат обединени в един ресурсен файл с име <code>_css.min.css</code> и запазен в папката на текущата ви тема. Не забравяте, че генерираните вече кеш файлове *може* да включват току-що изтрития CSS файл, трябва да регенерирате тези файлове или да регенерирате CSS файла. KiB Уверете се, че всеки URL вътре в оригиналния CSS код е абсолютен, не релативен (в противен случай ще ви липсват фоновите картинки). Също се уверете, че въвеждате URL на файловете точно както са в оригиналната страница / публикация (напр. има CSS линкове, които имат динамично съдържание, като: <u>http://www.example.com/color.php?ver=1.2</u>), иначе минимизирането и комбинирането няма да работи. Ако промените оригиналните файлове не забравяйте да запазите настройките тук, с цел регенериране на кеширания ресурсен CSS. Уверете се, че папката съществува и може да се записва в нея, т.е. правата трябва да са 755 или по-високи. Уверете се, че извикването на функцията <code>wp_footer();</code> е в самия край на файла от темата <code>footer.php</code>, точно преди затварящия таг <code>< /body></code>. В противен случай съдържанието след <code>wp_footer();</code> би могло да не бъде включено в генерирания кеш файл. MiB Минимизиран и комбиниран CSS файл Минимизиран и комбиниран CSS Минимизиране и комбиниране на CSS Минимизиране на кеш файла Няма данни Не Няма намерени кеш файлове или кеш файловете не могат да бъдат изтрити Функцията не е налична Брой намерени кеш файлове Страници Параметри Публикации Пресъздай кеширания файл при обновяване на страница/публикация/коментар Опресняване на статистиката Не забравяйте да излезете от профила или да използвате друг браузър ако искате да тествате дали кеширането работи, само нерегистрираните потребители могат да се ползват от това. Отмяна Възстанови настройките Запазване на настройките Заявките за търсене или индивидуалните страници и публикации могат да бъдат изключени от кеширането. Изберете страници или публикации от вече издадените, които не искате да бъдат кеширани, сортирани по заглавие, скритите страници и публикации също са в този списък. Изключването би било полезно когато имате регистрационна форма или каквато и да е друга модифицирана (специфична) форма, намираща се на тези страници. Изберете, за да бъде автоматично създаден кеширащ файл на диска, когато страница или публикация са модифицирани (включително когато се добавя или обновява коментар, което предизвиква всъщност обновяването на броя на коментарите към страницата/публикацията), опцията е полезна ако не искате потребителите да чакат кешът да изтече, за да видят последните промени по страниците/публикациите/коментарите. Изберете, за да кеширате или не резултатите от търсене в уеб сайта си. Би било полезно да изберете 'Не' ако имате огромен трафик, генериран от търсения с цел ускоряване на визуализацията на страниците/публикациите. Изберете, за да разрешите или забраните кеширащия механизъм глобално в блога. Изберете, за да оптимизирате запазения на диска кеш файл за допълнително ускоряване на производителността, премахват се излишните интервали, нови редове от сорса и т.н., средно този процес осигурява редуциране на размера на кеш файла между 6 и 12%, в зависимост от форматирането на HTML съдържанието на некеширания файл. Ускорете уеб сайта си с настройките на механизма за кеширане. Статистика Опцията <code>allow_url_fopen</code> е настроена на <code>Off</code>във вашата <code>php.ini</code> конфигурация, CSS минимизирането и комбинирането не е възможно. Свържете се хостинг компанията си или администратора на уеб сървъра, за да разберете как и дали това може да бъде променено. Кешираните файлове автоматично се обновяват/изтриват когато страници/публикации са обновени (включително когато се добавя или обновява коментар, което предизвиква всъщност обновяването на броя на коментарите към страницата/публикацията) или изтрити перманентно. Настройките по подразбиране Стойността по подразбиране е подпапка, наречена < cached > в папката за качване по подразбиране на WordPress 'uploads'. Оставете това поле празно, за да възстановите пътя по подразбиране. Плъгинът създава кеш файл за всяка страница или публикация в избраната кеш папка, в зависимост от параметрите. Настройките Тези стойности показват текущите версии на софтуера, средното натоварване на системата за послените 1, 5 и 15 минути, също и заделената оперативна памет (текуща и максимална). Този файл ще бъде включен във всеки генериран кеширан файл, старите линкове ще бъдат премахнати. Общо място, заето от кеш файловете URL на CSS файловете, по един на ред Стойност в минути, само цяло число, по-голямо от нула Нива над 80 означават, че сървъра е претоварен. Внимание: Кеширането е изключено, блогът ви ще показва нормални страници на потребителите Внимание: избраната папка за кеш файловете не може да бъде създадена, изберете друга или блогът няма да функционира коректно Да bytes д ч мин сек седм.  бяха запазени 
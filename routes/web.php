<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => Config::get('awebooking.prefix_dashboard'), 'middleware' => ['authenticate', 'locale']], function () {

    Route::get('/', 'DashboardController@index')->name('dashboard');

    Route::get('menus', 'MenuController@index')->name('menus');

    Route::post('update-menu', 'MenuController@updateMenuAction');

    Route::post('delete-menu', 'MenuController@deleteMenuAction');

    // Options route
    Route::get('settings', 'OptionController@_getSetting')->name('settings');

    Route::post('settings', 'OptionController@_saveSetting')->name('save-settings');

    Route::post('save-quick-settings', 'OptionController@_saveQuickSetting')->name('save-quick-settings');

    Route::post('set-featured-image', 'OptionController@_setFeaturedImage')->name('set-featured-image');

    Route::post('delete-featured-image', 'OptionController@_deleteFeaturedImage')->name('delete-featured-image');

    Route::post('get-list-item', 'OptionController@_getListItem')->name('get-list-item');

    //Media route
    Route::get('media', 'MediaController@_getMedia')->name('media');

    Route::post('add-media', 'MediaController@_addMedia')->name('add-media');

    Route::post('delete-media-item', 'MediaController@_deleteMediaItem')->name('delete-media-item');

    Route::post('all-media', 'MediaController@_allMedia')->name('all-media');

    Route::post('media-item-detail', 'MediaController@_mediaItemDetail')->name('media-item-detail');

    Route::post('update-media-item-detail', 'MediaController@_updateMediaItemDetail')->name('update-media-item-detail');

    Route::post('get-attachments', 'MediaController@_getAttachments')->name('get-attachments');

    Route::post('get-advance-attachments', 'MediaController@_getAdvanceAttachments')->name('get-advance-attachments');

    //Profile
    Route::get('profile', 'DashboardController@_getProfile')->name('profile');

    Route::post('update-your-profile', 'DashboardController@_updateYourProfile')->name('update-your-profile');

    Route::post('update-your-avatar', 'DashboardController@_updateYourAvatar')->name('update-your-avatar');

	Route::post('update-password', 'DashboardController@_updatePassword')->name('update-password');

    // Home route
    Route::get('my-home/{page?}', 'HomeController@_myHome')->name('my-home');

    Route::get('add-new-home', 'HomeController@_addNewHome')->name('add-new-home');

    Route::get('edit-home/{home_id}', 'HomeController@_editHome')->name('edit-home');

    Route::post('post-new-home', 'HomeController@_updateHome')->name('post-new-home');

    Route::post('change-status-home', 'HomeController@_changeStatusHome')->name('change-status-home');

    Route::post('delete-home-item', 'HomeController@_deleteHomeItem')->name('delete-home-item');

    Route::get('home-review{page?}', 'HomeController@_homeReview')->name('home-review');

    // Home taxonomy route
    Route::get('home-type/{page?}', 'TermController@_homeType')->name('home-type');

    Route::post('add-new-term', 'TermController@_addNewTerm')->name('add-new-term');

    Route::post('get-home-type-item', 'TermController@_getHomeTypeItem')->name('get-home-type-item');

    Route::post('update-term-item', 'TermController@_updateTermItem')->name('update-term-item');

    Route::post('delete-term-item', 'TermController@_deleteTermItem')->name('delete-term-item');

    Route::get('home-amenity/{page?}', 'TermController@_homeAmenity')->name('home-amenity');

    Route::post('get-home-amenity-item', 'TermController@_getHomeAmenityItem')->name('get-home-amenity-item');

    //Icon route
    Route::post('get-font-icon', 'DashboardController@_getFontIcon')->name('get-font-icon');

    //Coupon route
    Route::get('coupon/{page?}', 'CouponController@_allCoupon')->name('coupon');

    Route::post('add-new-coupon', 'CouponController@_addNewCoupon')->name('add-new-coupon');

    Route::post('change-coupon-status', 'CouponController@_changeCouponStatus')->name('change-coupon-status');

    Route::post('get-coupon-item', 'CouponController@_getCouponItem')->name('get-coupon-item');

    Route::post('update-coupon-item', 'CouponController@_updateCouponItem')->name('update-coupon-item');

    Route::post('delete-coupon-item', 'CouponController@_deleteCouponItem')->name('delete-coupon-item');

    // Custom price route
    Route::post('add-new-custom-price', 'CustomPriceController@_addNewCustomPrice')->name('add-new-custom-price');

    Route::post('delete-custom-price-item', 'CustomPriceController@_deleteCustomPriceItem')->name('delete-custom-price-item');

    Route::post('change-home-price-status', 'CustomPriceController@_changeStatusCustomPriceItem')->name('change-home-price-status');

    //Route Page
    Route::get('all-page/{page?}', 'PageController@_allPage')->name('all-page');

    Route::get('add-new-page', 'PageController@_addNewPage')->name('add-new-page');

    Route::post('add-new-page', 'PageController@_addNewPageAction');

    Route::get('edit-page/{id?}', 'PageController@_editPage')->name('edit-page');

    Route::post('edit-page', 'PageController@_editPageAction');

    Route::post('delete-page-item', 'PageController@_deletePageAction');

    //Route Post
    Route::get('all-post/{page?}', 'PostController@_allPost')->name('all-post');

    Route::get('add-new-post', 'PostController@_addNewPost')->name('add-new-post');

    Route::post('add-new-post', 'PostController@_addNewPostAction');

    Route::get('edit-post/{id?}', 'PostController@_editPost')->name('edit-post');

    Route::post('edit-post', 'PostController@_editPostAction');

    Route::post('delete-post-item', 'PostController@_deletePostAction');

    Route::get('post-category/{page?}', 'TermController@_postCategory')->name('post-category');

    Route::post('get-post-category-item', 'TermController@_getPostCategoryItem');

    Route::get('post-tag/{page?}', 'TermController@_postTag')->name('post-tag');

    Route::post('get-post-tag-item', 'TermController@_getPostTagItem');

    Route::post('get-booking-invoice', 'BookingController@_getBookingInvoice');

    Route::post('change-booking-status', 'BookingController@_changeBookingStatus');

    Route::get('all-booking/{page?}', 'BookingController@_allBooking')->name('all-booking');

    Route::get('comment/{page?}', 'PostController@_postComment')->name('comment');

    //Comment Route
    Route::post('delete-review-item', 'CommentController@_deleteReviewAction');

    Route::post('change-review-status', 'CommentController@_changeReviewStatusAction');

    // Booking
    Route::get('booking-confirmation', 'BookingController@_bookingConfirmation');

    // User
    Route::get('user-management/{page?}', 'UserController@_userManagement')->name('user-management');

    Route::post('add-new-user', 'UserController@_addNewUser')->name('add-new-user');

    Route::post('delete-user', 'UserController@_deleteUser')->name('delete-user');

    Route::post('get-user-item', 'UserController@_getUserItem');

    Route::post('update-user-item', 'UserController@_updateUserItem');

    //Package membership

    //Route::get('package/{page?}', 'PackageController@_allPackages')->name('package');

    //Route::post('add-new-package', 'PackageController@_addNewPackage');

    //Route::post('get-package-item', 'PackageController@_getPackageItem');

    //Route::post('update-package-item', 'PackageController@_updatePackageItem');

    //Route::post('delete-package-item', 'PackageController@_deletePackageItem');

    //Route::get('list-package', 'PackageController@_getListPackage')->name('list_package');

    Route::post('get-inventory', 'OptionController@_getInventory')->name('get-inventory');
    Route::post('get-availability-time-slot', 'OptionController@_getAvailabilityTimeSlot')->name('get-inventory');

    Route::get('import-data', 'ImportController@index')->name('import-data');
    Route::post('import-data', 'ImportController@_adminImportData');

    //Translation
	Route::get('translation', 'TranslationController@index')->name('translation');
	Route::post('update-translate', 'TranslationController@translateString')->name('update-translate');
	Route::post('scan-translation', 'TranslationController@scanTranslate')->name('scan-translate');

    //Languages
    Route::get('language/{page?}', 'TranslationController@language')->name('language');
    Route::post('update-language', 'TranslationController@updateLanguage')->name('update-language');
    Route::post('change-language-status', 'TranslationController@changeLanguageStatus')->name('change-language-status');
    Route::post('delete-language-item', 'TranslationController@deleteLanguageItem')->name('delete-language-item');
    Route::post('change-language-order', 'TranslationController@changeLanguageOrder')->name('change-language-order');
});

Route::group(['prefix' => Config::get('awebooking.prefix_auth'), 'middleware' => ['isLogin', 'locale']], function () {

    Route::get('login', 'AuthController@_getLogin')->name('login');

    Route::post('login', 'AuthController@_postLogin')->name('post.login');

});

Route::group(['prefix' => Config::get('awebooking.prefix_auth'), 'middleware' => 'locale'], function () {

    Route::post('logout', 'AuthController@_postLogout')->name('post.logout');

});

Route::group(['prefix' => Config::get('awebooking.prefix_auth'), 'middleware' => 'locale'], function () {

    Route::get('reset-password', 'AuthController@_getResetPassword')->name('get.reset.password');

    Route::post('reset-password', 'AuthController@_postResetPassword')->name('post.reset.password');

    Route::get('sign-up', 'AuthController@_getSignUp')->name('get.sign.up');

    Route::post('sign-up', 'AuthController@_postSignUp')->name('post.sign.up');
});

Route::group(['prefix' => Config::get('awebooking.prefix_dashboard'), 'middleware' => ['authenticate', 'locale']], function () {

    Route::get('all-notifications/{page?}', 'NotificationController@_allNotifications')->name('all-notifications');

    Route::post('delete-notification', 'NotificationController@_deleteNotification')->name('delete-notification');
});

Route::group(['middleware' => 'locale'], function() {
// Notification
	Route::post( 'update-last-time-check-notification', 'NotificationController@_updateLastcheckNoti' );

	Route::get( Config::get( 'awebooking.post_types' )['home']['slug'] . '/{home_name?}', 'HomeController@_getHomeSingle' )->name( Config::get( 'awebooking.post_types' )['home']['slug'] );

	Route::get( Config::get( 'awebooking.checkout_slug' ), 'CheckoutController@_checkoutPage' );

	Route::get( Config::get( 'awebooking.after_checkout_slug' ), 'CheckoutController@_thankyouPage' );

	Route::post( 'get-home-availability-single', 'HomeController@_getHomeAvailabilitySingle' );

	Route::post( 'get-home-availability-time-single', 'HomeController@_getHomeAvailabilityTimeSingle' );

	Route::post( 'get-home-price-realtime', 'HomeController@_getHomePriceRealTime' );

	Route::post( 'add-to-cart-home', 'HomeController@_addToCartHome' );

	Route::post( 'add-coupon', 'CouponController@_addCouponToCart' );

	Route::post( 'remove-coupon', 'CouponController@_removeCouponFromCart' );

	Route::post( 'checkout', 'CheckoutController@_checkoutAction' );

	Route::get( 'page-search-result', 'SearchController@searchPage' )->name( 'page-search-result' );

	Route::post( 'get-search-result', 'SearchController@getSearchResult' );

	Route::get( 'page/{page_slug?}', 'PageController@viewPage' )->name( 'view-page' );

	Route::get( 'post/{post_slug?}', 'PostController@viewPost' )->name( 'post' );

	Route::get( 'blog/{page?}', 'PostController@viewBlog' )->name( 'blog' );

	Route::get( 'category/{term_slug}/{page?}', 'PostController@viewCategory' )->name( 'category' );

	Route::get( 'tag/{term_slug}/{page?}', 'PostController@viewTag' )->name( 'tag' );

	Route::post( 'add-post-comment', 'CommentController@addCommentAction' );

	Route::get( '/', 'HomePageController@index' )->name( 'home-page' );

    // Enquiry form
    Route::post( 'send-enquiry-form', 'HomeController@_sendEnquiryForm' );

	Route::post( 'subscribe-email', 'AuthController@subscribeEmail' );

// Ajax get data
	Route::post( 'get-home-near-you-ajax', 'HomeController@_getHomeNearYouAjax' );

	Route::post( 'get-latest-home-ajax', 'HomeController@_getLatestHomeAjax' );

// Social login
	Route::get( 'social-login/{social?}', 'SocialController@_checkLogin' );

// Contact page
	Route::get( 'contact-us', 'HomePageController@_contactPage' )->name('contact-us');

	Route::post( 'contact-us-post', 'HomePageController@_contactUsPost' );

	Route::post( 'import-demo', 'ImportController@_runImport' );
});

Route::get('/artisan/storage', function() {
    $command = 'storage:link';
    Artisan::call($command);
    return Artisan::output();
});
Route::get('/artisan/cache', function() {
    $command = 'view:clear';
    Artisan::call($command);
    return Artisan::output();
});
Route::get( 'update-system', 'UpdateController@_update' );

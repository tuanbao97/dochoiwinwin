<?php
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\StorefrontAccountPageController;
use App\Http\Controllers\Auth\StorefrontSessionController;
use App\Http\Controllers\ThemeStorefrontController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Cors;
use App\Service\NewsService;
use App\Service\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

Route::post('/account/login', [StorefrontSessionController::class, 'login'])->name('storefront.session.login');
Route::post('/account/session', [StorefrontSessionController::class, 'store'])->name('storefront.session.store');
Route::post('/account/session/token', [StorefrontSessionController::class, 'token'])->name('storefront.session.token');
Route::post('/account/logout', [StorefrontSessionController::class, 'destroy'])->name('storefront.session.destroy');

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->whereIn('provider', ['google', 'facebook'])
    ->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->whereIn('provider', ['google', 'facebook'])
    ->name('social.callback');

Route::middleware(['count-client-view-website'])->group(function() {

    // Trang chủ
    Route::get('/', function () {
        return view('UI-FRONTEND/index');
    });
    Route::get('trang-chu', function () {
        return view('UI-FRONTEND/index');
    });

    // Các trang chính
    Route::get('gioi-thieu', function () {
        return view('UI-FRONTEND/gioi-thieu/index', ['productId' => 0]);
    });
    Route::get('lien-he', function () {
        return view('UI-FRONTEND/lien-he/index', ['productId' => 0]);
    });
    Route::get('chinh-sach-bao-hanh', function () {
        return view('UI-FRONTEND/chinh-sach-bao-hanh/index', ['productId' => 0]);
    });
    Route::get('bao-hanh', function () {
        return redirect('/chinh-sach-bao-hanh');
    });
    Route::get('chinh-sach-thanh-toan', function () {
        return view('UI-FRONTEND/chinh-sach-thanh-toan/index', ['productId' => 0]);
    });

    Route::get('tin-tuc/chi-tiet/{newsKey}', [ThemeStorefrontController::class, 'newsDetail'])
        ->where('newsKey', '.+');
    Route::get('tin-tuc/danh-muc/{categoryKey}/trang/{page}', [ThemeStorefrontController::class, 'newsList'])
        ->where('categoryKey', '[^/]+')
        ->where('page', '[0-9]+');
    Route::get('tin-tuc/danh-muc/{categoryKey}', [ThemeStorefrontController::class, 'newsList'])
        ->where('categoryKey', '[^/]+');
    Route::get('tin-tuc/trang/{page}', [ThemeStorefrontController::class, 'newsList'])
        ->where('page', '[0-9]+');
    Route::get('tin-tuc', [ThemeStorefrontController::class, 'newsList']);

    Route::get('video/trang/{page}', [ThemeStorefrontController::class, 'videoList'])
        ->where('page', '[0-9]+');
    Route::get('video', [ThemeStorefrontController::class, 'videoList']);

    // Account
    Route::get('account/login', [StorefrontAccountPageController::class, 'login']);
    Route::get('account/login/quan-tri', [StorefrontAccountPageController::class, 'staffLogin']);
    Route::get('account/orders', [StorefrontAccountPageController::class, 'orders']);
    Route::get('account/profile', [StorefrontAccountPageController::class, 'profile']);

    // API giỏ hàng & sản phẩm theme UI-FRONTEND
    // Dùng /cart/add (không .js) vì nginx production thường trả 404 cho URL dạng *.js như file tĩnh.
    Route::post('cart/add', [ThemeStorefrontController::class, 'cartAdd']);
    Route::post('cart/add.js', [ThemeStorefrontController::class, 'cartAdd']); // tương thích cũ
    Route::get('cart/change', [ThemeStorefrontController::class, 'cartChange']);
    Route::post('cart/update', [ThemeStorefrontController::class, 'cartUpdate']);
    Route::post('cart/update.js', [ThemeStorefrontController::class, 'cartUpdate']); // tương thích cũ
    Route::post('cart/clear', [ThemeStorefrontController::class, 'cartClear']);
    Route::get('cart/recommendations', [ThemeStorefrontController::class, 'cartRecommendations']);
    Route::get('cart', [ThemeStorefrontController::class, 'cartPage']);
    Route::get('thanh-toan', [ThemeStorefrontController::class, 'checkoutPage']);

    // Listing SP dạng path (/danh-muc/.../gia/.../sap-xep/.../trang/N)
    Route::get('danh-muc/{categoryKey}/{filters?}', [ThemeStorefrontController::class, 'categoryListing'])
        ->where('categoryKey', '[^/]+')
        ->where('filters', '.*');
    Route::get('tim-kiem/{query}/{filters?}', [ThemeStorefrontController::class, 'keywordListing'])
        ->where('query', '[^/]+')
        ->where('filters', '.*');
    Route::get('san-pham-vip/{filters?}', [ThemeStorefrontController::class, 'vipListing'])
        ->where('filters', '.*');
    Route::get('san-pham-noi-bat/{filters?}', [ThemeStorefrontController::class, 'hotListing'])
        ->where('filters', '.*');
    Route::get('tat-ca-san-pham/{filters?}', [ThemeStorefrontController::class, 'allProducts'])
        ->where('filters', '.*');

    // /search giữ cho quick-search (?view=quick-search) + 301 URL cũ ?query=
    Route::get('search', [ThemeStorefrontController::class, 'search']);

    Route::get('san-pham/chi-tiet/{productKey}', [ThemeStorefrontController::class, 'productDetail'])
        ->where('productKey', '.+');

    Route::get('{themeProductSlug}', [ThemeStorefrontController::class, 'productOrQuickview'])
        ->where('themeProductSlug', '[a-z0-9][a-z0-9\-]{0,200}');

});

/* Template source */
Route::group(['prefix' => 'quan-ly/template'], function () {

    Route::group(['prefix' => 'dashboard'], function() {
        Route::get('dashboard', function () {
            return view('UI-BACKEND/template/dashboard/dashboard');
        });
    });
    
    Route::group(['prefix' => 'widget'], function() {
        Route::get('widget', function () {
            return view('UI-BACKEND/template/widget/widget');
        });
    });

    Route::group(['prefix' => 'ui-elements'], function() {
        Route::get('accordion', function () {
            return view('UI-BACKEND/template/ui-elements/accordion');
        });
        
        Route::get('/button', function() {
            return view('UI-BACKEND/template/ui-elements/button');
        });
            
        Route::get('/badges', function() {
            return view('UI-BACKEND/template/ui-elements/badges');
        });
                
        Route::get('/breadcrumbs', function() {
            return view('UI-BACKEND/template/ui-elements/breadcrumbs');
        });
                    
        Route::get('/dropdown', function() {
            return view('UI-BACKEND/template/ui-elements/dropdown');
        });
                        
        Route::get('/modal', function() {
            return view('UI-BACKEND/template/ui-elements/modal');
        });
        
        Route::get('/progress', function() {
            return view('UI-BACKEND/template/ui-elements/progress');
        });
                                
        Route::get('/pagination', function() {
            return view('UI-BACKEND/template/ui-elements/pagination');
        });
                                    
        Route::get('/tabs', function() {
            return view('UI-BACKEND/template/ui-elements/tabs');
        });
                                        
        Route::get('/typography', function() {
            return view('UI-BACKEND/template/ui-elements/typography');
        });
                                            
        Route::get('/tooltips', function() {
            return view('UI-BACKEND/template/ui-elements/tooltips');
        });
    });

    Route::group(['prefix' => 'advanced-ui'], function() {
        Route::get('/dragula', function() {
            return view('UI-BACKEND/template/advanced-ui/dragula');
        });
            
        Route::get('/clipboard', function() {
            return view('UI-BACKEND/template/advanced-ui/clipboard');
        });
                
        Route::get('/context-menu', function() {
            return view('UI-BACKEND/template/advanced-ui/context-menu');
        });
                    
        Route::get('/slider', function() {
            return view('UI-BACKEND/template/advanced-ui/slider');
        });
                        
        Route::get('/carousel', function() {
            return view('UI-BACKEND/template/advanced-ui/carousel');
        });

        Route::get('/colcade', function() {
            return view('UI-BACKEND/template/advanced-ui/colcade');
        });
                                    
        Route::get('/loader', function() {
            return view('UI-BACKEND/template/advanced-ui/loader');
        });
    });

    Route::group(['prefix' => 'form-elements'], function() {
        Route::get('/basic-element', function() {
            return view('UI-BACKEND/template/form-elements/basic-element');
        });
            
        Route::get('/advanced-element', function() {
            return view('UI-BACKEND/template/form-elements/advanced-element');
        });

        Route::get('/validation', function() {
            return view('UI-BACKEND/template/form-elements/validation');
        });
            
        Route::get('/jquery-steps-wizard', function() {
            return view('UI-BACKEND/template/form-elements/jquery-steps-wizard');
        });
    });

    Route::group(['prefix' => 'editor'], function() {
        Route::get('/text-editor', function() {
            return view('UI-BACKEND/template/editor/text-editor');
        });
            
        Route::get('/code-editor', function() {
            return view('UI-BACKEND/template/editor/code-editor');
        });
            
    });

    Route::group(['prefix' => 'table'], function() {
        Route::get('/basic-table', function() {
            return view('UI-BACKEND/template/table/basic-table');
        });
            
        Route::get('/data-table', function() {
            return view('UI-BACKEND/template/table/data-table');
        });
            
        Route::get('/js-grid', function() {
            return view('UI-BACKEND/template/table/js-grid');
        });
                
        Route::get('/sort-table', function() {
            return view('UI-BACKEND/template/table/sort-table');
        });
                
    });

    Route::group(['prefix' => 'chart'], function() {
        Route::get('/chart-js', function() {
            return view('UI-BACKEND/template/chart/chart-js');
        });
            
        Route::get('/morris', function() {
            return view('UI-BACKEND/template/chart/morris');
        });
            
        Route::get('/google-chart', function() {
            return view('UI-BACKEND/template/chart/google-chart');
        });
                
        Route::get('/c3-chart', function() {
            return view('UI-BACKEND/template/chart/c3-chart');
        });
                    
        Route::get('/just-gage', function() {
            return view('UI-BACKEND/template/chart/just-gage');
        });
                
    });

    Route::group(['prefix' => 'popup'], function() {
        Route::get('/popup', function() {
            return view('UI-BACKEND/template/popup/popup');
        });
                
    });

    Route::group(['prefix' => 'notification'], function() {
        Route::get('/notification', function() {
            return view('UI-BACKEND/template/notification/notification');
        });
            
    });

    Route::group(['prefix' => 'icons'], function() {
        Route::get('/flag-icon', function() {
            return view('UI-BACKEND/template/icons/flag-icon');
        });
           
        Route::get('/mdi-icon', function() {
            return view('UI-BACKEND/template/icons/mdi-icon');
        });
            
        Route::get('/font-awesome', function() {
            return view('UI-BACKEND/template/icons/font-awesome');
        });
                    
        Route::get('/simple-line-icon', function() {
            return view('UI-BACKEND/template/icons/simple-line-icon');
        });
                        
        Route::get('/themify-icon', function() {
            return view('UI-BACKEND/template/icons/themify-icon');
        });
    });

    Route::group(['prefix' => 'user-pages'], function() {
        Route::get('/login', function() {
            return view('UI-BACKEND/template/user-pages/login');
        });
            
        Route::get('/login-2', function() {
            return view('UI-BACKEND/template/user-pages/login-2');
        });
                
        Route::get('/register', function() {
            return view('UI-BACKEND/template/user-pages/register');
        });
                    
        Route::get('/register-2', function() {
            return view('UI-BACKEND/template/user-pages/register-2');
        });
                        
        Route::get('/lock-screen', function() {
            return view('UI-BACKEND/template/user-pages/lock-screen');
        });
            
    });

    Route::group(['prefix' => 'error-page'], function() {
        Route::get('/404', function() {
            return view('UI-BACKEND/template/error-page/404');
        });
            
        Route::get('/500', function() {
            return view('UI-BACKEND/template/error-page/500');
        });
            
    });

    Route::group(['prefix' => 'general-page'], function() {
        Route::get('/blank-page', function() {
            return view('UI-BACKEND/template/general-page/blank-page');
        });
            
        Route::get('/profile', function() {
            return view('UI-BACKEND/template/general-page/profile');
        });
                
        Route::get('/FAQ', function() {
            return view('UI-BACKEND/template/general-page/FAQ');
        });
                    
        Route::get('/FAQ-2', function() {
            return view('UI-BACKEND/template/general-page/FAQ-2');
        });
                        
        Route::get('/news-grid', function() {
            return view('UI-BACKEND/template/general-page/news-grid');
        });
                            
        Route::get('/timeline', function() {
            return view('UI-BACKEND/template/general-page/timeline');
        });
                                
        Route::get('/search-result', function() {
            return view('UI-BACKEND/template/general-page/search-result');
        });
                                    
        Route::get('/portfolio', function() {
            return view('UI-BACKEND/template/general-page/portfolio');
        });
                
    });

    Route::group(['prefix' => 'e-commerce'], function() {
        Route::get('/invoice', function() {
            return view('UI-BACKEND/template/e-commerce/invoice');
        });
            
        Route::get('/price-table', function() {
            return view('UI-BACKEND/template/e-commerce/price-table');
        });
            
        Route::get('/orders', function() {
            return view('UI-BACKEND/template/e-commerce/orders');
        });
                
    });

    Route::group(['prefix' => 'email'], function() {
        Route::get('/email', function() {
            return view('UI-BACKEND/template/email/email');
        });
    });

    Route::group(['prefix' => 'calendar'], function() {
        Route::get('/calendar', function() {
            return view('UI-BACKEND/template/calendar/calendar');
        });
    });

    Route::group(['prefix' => 'todo-list'], function() {
        Route::get('/todo-list', function() {
            return view('UI-BACKEND/template/todo-list/todo-list');
        });
    });

    Route::group(['prefix' => 'gallery'], function() {
        Route::get('/gallery', function() {
            return view('UI-BACKEND/template/gallery/gallery');
        });
    });
    

})->middleware(Cors::class);

Route::get('/login', function() {
    return redirect('/admin/login');
});

Route::get('/dangnhap', function() {
    return redirect('/admin/login');
});

Route::get('/dang-nhap', function() {
    return redirect('/admin/login');
});

Route::group(['prefix' => '/admin'], function() {
    
    Route::get('/login', function() {
        return view('UI-BACKEND/admin/auth/login/login');
    });

    Route::get('/register', function() {
        return view('UI-BACKEND/admin/auth/register/register');
    });

    Route::get('/forgot-password', function() {
        return view('UI-BACKEND/admin/auth/forgot-password/forgot-password');
    });

    Route::get('reset-password', function() {
        return view('UI-BACKEND/admin/auth/reset-password/reset-password');
    });

    Route::get('/cai-dat/{filters?}', function() {
        return view('UI-BACKEND/admin/cai-dat/cai-dat') ;
    })->where('filters', '.*');

    Route::get('/error/page-not-found', function() {
        return view('UI-BACKEND/admin/common/layout/errors/page-404') ;
    });

    Route::get('/nguoi-dung/danh-sach/{filters?}', function() {
        return view('UI-BACKEND/admin/nguoi-dung/danh-sach-nguoi-dung');
    })->where('filters', '.*');

    Route::get('/nguoi-dung', function() {
        return redirect('/admin/nguoi-dung/danh-sach');
    });

    Route::get('/nguoi-dung/chi-tiet', function() {
        return view('UI-BACKEND/admin/nguoi-dung/thong-tin-nguoi-dung' 
            , [
                'userId' => null
            ]
        );
    });

    Route::get('/nguoi-dung/chi-tiet/{userId}', function($userId) {
        $arrUserId = explode('-', $userId);
        return view('UI-BACKEND/admin/nguoi-dung/thong-tin-nguoi-dung' 
            , [
                'userId' => $arrUserId[count($arrUserId) - 1]
            ]
        );
    });

    Route::get('/danh-muc-san-pham/danh-sach/{filters?}', function() {
       return view('UI-BACKEND/admin/danh-muc-san-pham/danh-sach-danh-muc-san-pham') ;
    })->where('filters', '.*');

    Route::get('/danh-muc-san-pham', function() {
        return redirect('/admin/danh-muc-san-pham/danh-sach');
    });

    Route::get('/danh-muc-san-pham/chi-tiet', function() {
        return view('UI-BACKEND/admin/danh-muc-san-pham/chi-tiet-danh-muc-san-pham') ;
    });

    Route::get('/danh-muc-san-pham/chi-tiet/{thongTinId}', function($thongTinId) {
        $arrThongTinId = explode('-', $thongTinId);
        return view('UI-BACKEND/admin/danh-muc-san-pham/chi-tiet-danh-muc-san-pham', 
            [
                'categoryPId' => $arrThongTinId[count($arrThongTinId) - 1]
            ]
        ) ;
    });

    Route::get('/san-pham/chi-tiet', function() {
        return view('UI-BACKEND/admin/san-pham/chi-tiet-san-pham') ;
    });

    Route::get('/san-pham/danh-sach/{filters?}', function() {
        return view('UI-BACKEND/admin/san-pham/danh-sach-san-pham');
    })->where('filters', '.*');

    Route::get('/san-pham', function() {
        return redirect('/admin/san-pham/danh-sach');
    });

    Route::get('/san-pham/chi-tiet/{sanPhamId}', function($sanPhamId) {
        $arrSanPhamId = explode('-', $sanPhamId);
        return view('UI-BACKEND/admin/san-pham/chi-tiet-san-pham' 
            , [
                'productId' => $arrSanPhamId[count($arrSanPhamId) - 1]
            ]
        );
    });


    Route::get('/danh-muc-tin-tuc/danh-sach/{filters?}', function() {
       return view('UI-BACKEND/admin/danh-muc-tin-tuc/danh-sach-danh-muc-tin-tuc') ;
    })->where('filters', '.*');

    Route::get('/danh-muc-tin-tuc', function() {
        return redirect('/admin/danh-muc-tin-tuc/danh-sach');
    });

    Route::get('/danh-muc-tin-tuc/chi-tiet', function() {
        return view('UI-BACKEND/admin/danh-muc-tin-tuc/chi-tiet-danh-muc-tin-tuc') ;
    });

    Route::get('/danh-muc-tin-tuc/chi-tiet/{thongTinId}', function($thongTinId) {
        $arrThongTinId = explode('-', $thongTinId);
        return view('UI-BACKEND/admin/danh-muc-tin-tuc/chi-tiet-danh-muc-tin-tuc', 
            [
                'categoryNId' => $arrThongTinId[count($arrThongTinId) - 1]
            ]
        ) ;
    });

     Route::get('/tin-tuc/chi-tiet', function() {
        return view('UI-BACKEND/admin/tin-tuc/chi-tiet-tin-tuc') ;
    });

    Route::get('/thong-tin-ca-nhan', function() {
        return view('UI-BACKEND/admin/thong-tin-ca-nhan/thong-tin-ca-nhan');
    });

    Route::get('/doi-mat-khau', function() {
        return view('UI-BACKEND/admin/thong-tin-ca-nhan/doi-mat-khau');
    });

    Route::get('/tin-tuc/danh-sach/{filters?}', function() {
        return view('UI-BACKEND/admin/tin-tuc/danh-sach-tin-tuc');
    })->where('filters', '.*');

    Route::get('/tin-tuc', function() {
        return redirect('/admin/tin-tuc/danh-sach');
    });

    Route::get('/tin-tuc/chi-tiet', function() {
        return view('UI-BACKEND/admin/tin-tuc/chi-tiet-tin-tuc');
    });

    Route::get('/tin-tuc/chi-tiet/{tinTucId}', function($tinTucId) {
        $arrTinTucId = explode('-', $tinTucId);
        return view('UI-BACKEND/admin/tin-tuc/chi-tiet-tin-tuc',
            [
                'newsId' => $arrTinTucId[count($arrTinTucId) - 1]
            ]
        );
    });

    Route::get('/video/danh-sach/{filters?}', function() {
        return view('UI-BACKEND/admin/video/danh-sach-video');
    })->where('filters', '.*');

    Route::get('/video', function() {
        return redirect('/admin/video/danh-sach');
    });

    Route::get('/video/chi-tiet', function() {
        return view('UI-BACKEND/admin/video/chi-tiet-video');
    });

    Route::get('/video/chi-tiet/{videoId}', function($videoId) {
        $arrVideoId = explode('-', $videoId);
        return view('UI-BACKEND/admin/video/chi-tiet-video',
            [
                'videoId' => $arrVideoId[count($arrVideoId) - 1]
            ]
        );
    });

    Route::get('/don-hang/danh-sach/{filters?}', function() {
        return view('UI-BACKEND/admin/don-hang/danh-sach-don-hang');
    })->where('filters', '.*');

    Route::get('/don-hang', function() {
        return redirect('/admin/don-hang/danh-sach');
    });

    Route::get('/don-hang/chi-tiet/{transactionId}', function($transactionId) {
        $arrId = explode('-', $transactionId);
        return view('UI-BACKEND/admin/don-hang/chi-tiet-don-hang',
            [
                'transactionId' => $arrId[count($arrId) - 1]
            ]
        );
    });

});

Route::post('/product/view', function(Request $request) {
    $pathView = $request->input('pathView', null);
    $productId = $request->input('productId', null);
    $duLieu = $request->input('duLieu', null);
    $uuid = $request->input('uuid', null) ?? Str::random(6);

    return view($pathView
        , [
            'productId' => $productId
            , 'duLieu' => $duLieu
            , 'uuid' => $uuid
        ]
    );
});

Route::post('/news/view', function(Request $request) {
    $pathView = $request->input('pathView', null);
    $newsId = $request->input('newsId', null);
    $duLieu = $request->input('duLieu', null);
    $uuid = $request->input('uuid', null) ?? Str::random(6);

    return view($pathView
        , [
            'newsId' => $newsId
            , 'duLieu' => $duLieu
            , 'uuid' => $uuid
        ]
    );
});

Route::post('/video/view', [App\Http\Controllers\Admin\VideoViewController::class, 'loadView'])->name('video.view');

Route::post('/html-box-upload-1-file/view', function(Request $request) {
    $uuid = $request->input('UUID', null) ?? Str::random(6);
    $title = $request->input('TITLE', null);
    return view('UI-BACKEND/admin/common/component/upload-file/html-box-upload-1-file'
        , [
            'uuid' => $uuid,
            'title' => $title
        ]
    );
});
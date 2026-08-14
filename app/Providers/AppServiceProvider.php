<?php
namespace App\Providers;

use App\Repository\DistrictRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductDocumentStorageRepository;
use App\Repository\ProductRepository;
use App\Repository\ProvinceRepository;
use App\Repository\WardRepository;
use App\Repository\impl\DistrictRepositoryImpl;
use App\Repository\impl\OauthClientRepositoryImpl;
use App\Repository\impl\ProductCategoryRepositoryImpl;
use App\Repository\impl\ProductDocumentStorageRepositoryImpl;
use App\Repository\impl\ProductRepositoryImpl;
use App\Repository\impl\ProductVariantRepositoryImpl;
use App\Repository\impl\ProvinceRepositoryImpl;
use App\Repository\impl\RoleRepositoryImpl;
use App\Repository\impl\SettingRepositoryImpl;
use App\Repository\impl\TitleRepositoryImpl;
use App\Repository\impl\UserRepositoryImpl;
use App\Repository\impl\WardRepositoryImpl;
use App\Repository\OauthClientRepository;
use App\Repository\ProductVariantRepository;
use App\Repository\RoleRepository;
use App\Repository\SettingRepository;
use App\Repository\TitleRepository;
use App\Repository\UserRepository;
use App\Service\AuthService;
use App\Service\DistrictService;
use App\Service\FFMpegService;
use App\Service\impl\AuthServiceImpl;
use App\Service\impl\FFMpegServiceImpl;
use App\Service\InterventionImageService;
use App\Service\ProductService;
use App\Service\ProvinceService;
use App\Service\WardService;
use App\Service\impl\DistrictServiceImpl;
use App\Service\impl\GdImageServiceImpl;
use App\Service\impl\MailServiceImpl;
use App\Service\impl\ProductServiceImpl;
use App\Service\impl\ProductVariantServiceImpl;
use App\Service\impl\ProvinceServiceImpl;
use App\Service\impl\RoleServiceImpl;
use App\Service\impl\SettingServiceImpl;
use App\Service\impl\TitleServiceImpl;
use App\Service\impl\UserServiceImpl;
use App\Service\impl\WardServiceImpl;
use App\Service\MailService;
use App\Service\ProductVariantService;
use App\Service\RoleService;
use App\Service\SettingService;
use App\Service\TitleService;
use App\Service\UserService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Laravel\Passport\Bridge\AccessTokenRepository;
use Laravel\Passport\Passport;
use League\OAuth2\Server\ResourceServer;
use App\Service\NewsService;
use App\Repository\NewsRepository;
use App\Repository\NewsDocumentStorageRepository;
use App\Repository\NewsCategoryRepository;
use App\Service\impl\NewsServiceImpl;
use App\Repository\impl\NewsRepositoryImpl;
use App\Repository\impl\NewsDocumentStorageRepositoryImpl;
use App\Repository\impl\NewsCategoryRepositoryImpl;
use App\Service\VideoService;
use App\Service\impl\VideoServiceImpl;
use App\Repository\VideoRepository;
use App\Repository\impl\VideoRepositoryImpl;
use App\Repository\VideoDocumentStorageRepository;
use App\Repository\impl\VideoDocumentStorageRepositoryImpl;
use App\Service\TransactionService;
use App\Service\impl\TransactionServiceImpl;
use App\Repository\TransactionRepository;
use App\Repository\impl\TransactionRepositoryImpl;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ResourceServer::class, function () {
            $accessTokenRepository = app(AccessTokenRepository::class);
            $publicKeyPath = Passport::keyPath('oauth-public.key');
            $privateKeyPath = Passport::keyPath('oauth-private.key');

            if (! is_file($publicKeyPath) || ! is_file($privateKeyPath)) {
                Artisan::call('passport:keys', ['--force' => true]);
            }

            if (! is_file($publicKeyPath)) {
                throw new \RuntimeException(
                    'OAuth public key file not found. Run: php artisan passport:keys'
                );
            }

            $publicKey = file_get_contents($publicKeyPath);

            return new ResourceServer($accessTokenRepository, $publicKey);
        });
        
        // Đăng ký bean AppService
        $this->app->singleton(\App\Service\AppService::class, \App\Service\impl\AppServiceImpl::class);

        // Đăng ký bean DocumentStorageService
        $this->app->singleton(\App\Service\DocumentStorageService::class, \App\Service\impl\DocumentStorageServiceImpl::class);

        // Đăng ký bean Category Production Service
        $this->app->singleton(\App\Service\CategoryPService::class, \App\Service\impl\CategoryPServiceImpl::class);

        // Đăng ký bean CategoryPRepository
        $this->app->singleton(\App\Repository\CategoryPRepository::class, \App\Repository\impl\CategoryPRepositoyImpl::class);

        // Đăng ký bean Category News Service
        $this->app->singleton(\App\Service\CategoryNService::class, \App\Service\impl\CategoryNServiceImpl::class);

        // Đăng ký bean CategoryNRepository
        $this->app->singleton(\App\Repository\CategoryNRepository::class, \App\Repository\impl\CategoryNRepositoyImpl::class);


        // Đăng ký bean DocumentStorageRepository
        $this->app->singleton(\App\Repository\DocumentStorageRepository::class, \App\Repository\impl\DocumentStorageRepositoryImpl::class);
        
        // Đăng ký bean CategoryPRepository
        $this->app->singleton(\App\Repository\CategoryPDocumentStorageRepository::class, \App\Repository\impl\CategoryPDocumentStorageRepositoryImpl::class);

        // Đăng ký bean CategoryNRepository
        $this->app->singleton(\App\Repository\CategoryNDocumentStorageRepository::class, \App\Repository\impl\CategoryNDocumentStorageRepositoryImpl::class);

        // Đăng ký bean ProvinceService
        $this->app->singleton(ProvinceService::class, ProvinceServiceImpl::class);

        // Đăng ký bean ProvinceRepository
        $this->app->singleton(ProvinceRepository::class, ProvinceRepositoryImpl::class);

        // Đăng ký bean DistrictService
        $this->app->singleton(DistrictService::class, DistrictServiceImpl::class);

        // Đăng ký bean DistrictRepository
        $this->app->singleton(DistrictRepository::class, DistrictRepositoryImpl::class);

        // Đăng ký bean WardService
        $this->app->singleton(WardService::class, WardServiceImpl::class);
        
        // Đăng ký bean WardRepository
        $this->app->singleton(WardRepository::class, WardRepositoryImpl::class);

        // Đăng ký bean ProductService
        $this->app->singleton(ProductService::class, ProductServiceImpl::class);

        // Đăng ký bean ProductRepository
        $this->app->singleton(ProductRepository::class, ProductRepositoryImpl::class);

        // Đăng ký bean ProductVariantService
        $this->app->singleton(ProductVariantService::class, ProductVariantServiceImpl::class);

        // Đăng ký bean ProductVariantRepository
        $this->app->singleton(ProductVariantRepository::class, ProductVariantRepositoryImpl::class);
        
        // Đăng ký bean ProductDocumentStorageRepository
        $this->app->singleton(ProductDocumentStorageRepository::class, ProductDocumentStorageRepositoryImpl::class);
    
        // Đăng ký bean ProducCategoryRepository
        $this->app->singleton(ProductCategoryRepository::class, ProductCategoryRepositoryImpl::class);

        // Đăng ký bean NewsService
        $this->app->singleton(NewsService::class, NewsServiceImpl::class);

        // Đăng ký bean NewsRepository
        $this->app->singleton(NewsRepository::class, NewsRepositoryImpl::class);

        // Đăng ký bean NewsDocumentStorageRepository
        $this->app->singleton(NewsDocumentStorageRepository::class, NewsDocumentStorageRepositoryImpl::class);
    
        // Đăng ký bean NewsCategoryRepository
        $this->app->singleton(NewsCategoryRepository::class, NewsCategoryRepositoryImpl::class);

        // Đăng ký bean Intervention Image
        $this->app->singleton(InterventionImageService::class, GdImageServiceImpl::class);
    
        // Đăng ký bean FFMpeg Service chỉnh sửa video
        $this->app->singleton(FFMpegService::class, FFMpegServiceImpl::class);

        // Đăng ký bean AuthService
        $this->app->singleton(AuthService::class, AuthServiceImpl::class);
    
        // Đăng ký bean UserService
        $this->app->singleton(UserService::class, UserServiceImpl::class);

        // Đăng ký bean UserRepository
        $this->app->singleton(UserRepository::class, UserRepositoryImpl::class);

        // Đăng ký bean OauthClientRepository
        $this->app->singleton(OauthClientRepository::class, OauthClientRepositoryImpl::class);

         // Đăng ký bean TitleService
        $this->app->singleton(TitleService::class, TitleServiceImpl::class);

        // Đăng ký bean TitleRepository
        $this->app->singleton(TitleRepository::class, TitleRepositoryImpl::class);

        // Đăng ký bean MailService
        $this->app->singleton(MailService::class, MailServiceImpl::class);

        // Đăng ký bean SettingService
        $this->app->singleton(SettingService::class, SettingServiceImpl::class);

        // Đăng ký bean SettingRepository
        $this->app->singleton(SettingRepository::class, SettingRepositoryImpl::class);

        // Đăng ký bean RoleService
        $this->app->singleton(RoleService::class, RoleServiceImpl::class);

        // Đăng ký bean RoleRepository
        $this->app->singleton(RoleRepository::class, RoleRepositoryImpl::class);

        // Đăng ký bean VideoService
        $this->app->singleton(VideoService::class, VideoServiceImpl::class);

        // Đăng ký bean VideoRepository
        $this->app->singleton(VideoRepository::class, VideoRepositoryImpl::class);

        // Đăng ký bean VideoDocumentStorageRepository
        $this->app->singleton(VideoDocumentStorageRepository::class, VideoDocumentStorageRepositoryImpl::class);

        // Đăng ký bean TransactionService
        $this->app->singleton(TransactionService::class, TransactionServiceImpl::class);

        // Đăng ký bean TransactionRepository
        $this->app->singleton(TransactionRepository::class, TransactionRepositoryImpl::class);

        // Sapo Private App API
        $this->app->singleton(\App\Service\SapoService::class, \App\Service\impl\SapoServiceImpl::class);

    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS chỉ khi ACTIVE_PROFILE = 'PROD'
        if (config('app.active_profile') === 'PROD') {
            URL::forceScheme('https');
        }

        DB::listen(function ($query) {
            Log::info($query->sql, [
                'bindings' => $query->bindings,
                'time' => $query->time
            ]);
        });

        /* PHẦN PASSPORT SECURITY */
        // Cấu hình enable grant password
        Passport::enablePasswordGrant();
        // Cấu hình TTL (Time to live) cho ACCESS_TOKEN - 30 ngày
        Passport::tokensExpireIn(now()->addDays(30));
        // Cấu hình TTL (Time to live) cho REFRSH_TOKEN - 60 ngày
        Passport::refreshTokensExpireIn(now()->addDays(60));
    }
}

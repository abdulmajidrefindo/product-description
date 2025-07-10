use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;

Route::prefix('products')->group(function () {
    Route::post('/', [ProductController::class, 'store']);
});

Route::prefix('categories')->group(function () {
    Route::post('/{product}', [CategoryController::class, 'store']);
    Route::post('/{category}/upload', [CategoryController::class, 'upload']);
    Route::delete('/{category}/image', [CategoryController::class, 'deleteImage']);
});

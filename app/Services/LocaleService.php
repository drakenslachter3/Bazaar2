<?php 

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class LocaleService
{
    public function availableLocales()
    {
        return [
            'nl' => 'Nederlands',
            'en' => 'English',
        ];
    }
    
    public function translateModel($model, $attribute)
    {
        $locale = App::getLocale();
        $fallbackLocale = config('app.fallback_locale');
        
        $translationKey = get_class($model) . '_' . $model->id . '_' . $attribute;
        
        if (Cache::has($translationKey . '_' . $locale)) {
            return Cache::get($translationKey . '_' . $locale);
        }
        
        // Zoek de vertaling in de database of gebruik de standaardtaal
        $translation = \App\Models\Translation::where('model', get_class($model))
            ->where('model_id', $model->id)
            ->where('attribute', $attribute)
            ->where('locale', $locale)
            ->first();
        
        if ($translation) {
            Cache::put($translationKey . '_' . $locale, $translation->value, 1440); // Cache voor 24 uur
            return $translation->value;
        }
        
        // Als er geen vertaling is, gebruik de attribuutwaarde
        Cache::put($translationKey . '_' . $locale, $model->$attribute, 1440);
        return $model->$attribute;
    }
    
    public function setModelTranslation($model, $attribute, $value, $locale = null)
    {
        if (!$locale) {
            $locale = App::getLocale();
        }
        
        $translation = \App\Models\Translation::updateOrCreate([
            'model' => get_class($model),
            'model_id' => $model->id,
            'attribute' => $attribute,
            'locale' => $locale,
        ], [
            'value' => $value,
        ]);
        
        $translationKey = get_class($model) . '_' . $model->id . '_' . $attribute;
        Cache::put($translationKey . '_' . $locale, $value, 1440);
        
        return $translation;
    }
}
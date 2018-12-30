<?php
namespace app\common\i18n;
use Yii;
use yii\base\InvalidConfigException;
use yii\i18n\MessageSource;

/**
 * Class I18N
 * @package Common\I18n
 * @copyright 2012-2019 Medkey
 */
class I18N extends \yii\i18n\I18N
{
    /**
     * @param string $category
     * @return object|MessageSource
     * @throws InvalidConfigException
     */
    public function getMessageSource($category)
    {
        if (isset($this->translations[$category])) {
            $source = $this->translations[$category];
            if ($source instanceof MessageSource) {
                return $source;
            } else {
                return $this->translations[$category] = Yii::createObject($source);
            }
        } else {
            // try wildcard matching
            foreach ($this->translations as $pattern => $source) {
                $category_parts = explode('/', $category);
                $pattern_parts = explode('/', $pattern);

                // добавил условие count($category_parts) == count($pattern_parts) для правильного поиска перевода подмодулей #90
                if (strpos($pattern, '*') > 0 && strpos($category, rtrim($pattern, '*')) === 0 && count($category_parts) == count($pattern_parts)) {
                    if ($source instanceof MessageSource) {
                        return $source;
                    } else {
                        return $this->translations[$category] = $this->translations[$pattern] = Yii::createObject($source);
                    }
                }
            }
            // match '*' in the last
            if (isset($this->translations['*'])) {
                $source = $this->translations['*'];
                if ($source instanceof MessageSource) {
                    return $source;
                } else {
                    return $this->translations[$category] = $this->translations['*'] = Yii::createObject($source);
                }
            }
        }

        throw new InvalidConfigException("Unable to locate message source for category '$category'.");
    }
}
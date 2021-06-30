class LanguageManager {
    public function loadLanguage() {
        $lang = $this->getBrowserLanuage();
        // 2) $lang is user input
        $sanitizedLang = $this->sanitizeLanguage($lang);
        // 4) file inclusion here
        require_once("/lang/$sanitizedlang");
    }

    private function getBrowserLanuage() {
        // 1) $lang is user input
        $lang = $_SERVER['HTTP_ACCETPT_LANGUAGE'] ?? 'en';
        return $lang;
    }

    private function sanitizeLanguage($language) {
        // 3) is not recursive --> can be bypassed
        return str_replace('../', '', $language);
    }
}
(new LanguageManager())->loadLanguage();


// example:
// echo str_replace('../', '', 'aaa/../..././bbb');
// aaa/../bbb


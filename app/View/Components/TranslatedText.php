<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\App;

class TranslatedText extends Component
{
    public $text;
    public $locale;
    
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($text)
    {
        $this->text = $text;
        $this->locale = App::getLocale();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        // Translate the text
        $translated = __($this->text);
        
        // If the translation didn't work, use the original text
        if ($translated === $this->text) {
            // Try fallback with capitalization variants
            $variants = [
                $this->text,
                ucfirst($this->text),
                ucwords($this->text),
                strtolower($this->text),
                strtoupper($this->text)
            ];
            
            foreach ($variants as $variant) {
                $translated = __($variant);
                if ($translated !== $variant) {
                    break;
                }
            }
        }
        
        return view('components.translated-text', ['translated' => $translated]);
    }
} 
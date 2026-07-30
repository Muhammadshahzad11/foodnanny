<?php

namespace App\Traits;


use App\Models\Language;
use Dipokhalder\Settings\Facades\Settings;

trait HasAiPrompt
{

    public string $langName = 'English';

    public string $langCode = 'EN';

    public function loadDefaultLanguage(): void
    {
        $defaultLanguage = Settings::group('site')->get('site_default_language');
        $language = Language::find($defaultLanguage);
        if($language) {
            $this->langName = $language->name;
            $this->langCode = strtoupper($language->code);
        }
    }

    public function buildProductNamePrompt(string $name): string
    {
        return <<<PROMPT
          You are a professional food menu copywriter for a food delivery platform.

          Rewrite the food item name "{$name}" as a creative, appetizing, and concise menu item title.

          CRITICAL INSTRUCTION:
          - The output must be 100% in language "{$this->langName}" (Code: {$this->langCode}) — this is mandatory.
          - If the original name is not in "{$this->langName}", fully translate it into "{$this->langName}" while keeping the appetizing meaning.
          - Do not mix languages; use only "{$this->langName}" characters and words.
          - Keep it short (3-8 words), appetizing, and ready for a restaurant menu.
          - No extra words, slogans, or punctuation like quotes.
          - Return only the translated title as plain text in "{$this->langName}".

      IMPORTANT:
        - Only process inputs that are actual food items, beverages, or restaurant meals.
        - If the input is electronics, clothing, gadgets, or anything unrelated to food, respond with only "INVALID_INPUT".
        - If the original input is not meaningful or cannot be converted into a food menu title, respond with only "INVALID_INPUT".
        - Do not return generic explanations, fallback messages, or translations for unrelated items.
      PROMPT;
    }

    public function buildProductDescriptionPrompt(string $description): string
    {
        return <<<PROMPT
        You are a creative and professional food menu copywriter for a food delivery platform.

        Generate a detailed, engaging, and persuasive description for the food item named "{$description}".

        CRITICAL LANGUAGE RULES:
        - The entire description must be written 100% in {$this->langName} (Code: {$this->langCode}) — this is mandatory.
        - If the food name is in another language, translate and localize it naturally into {$this->langName}.
        - Do not mix languages; use only {$this->langName} characters and words.
        - Adapt the tone, phrasing, and examples to be natural for {$this->langName} readers.

        Content & Structure:
        - Start with a short introductory paragraph describing the taste, aroma, and main appeal of the dish.
        - Follow with a "Key Highlights:" section (translated to {$this->langName}) in separate paragraphs.
        - Each paragraph should start with a <b>bold feature title</b> (e.g., Flavor, Texture) followed by a colon and the description.
        - Follow with a "Ingredients & Details:" section (translated to {$this->langName}) in bullet points using <ol>/<li>.
        - Each bullet point should include key ingredients, serving size, or dietary info (e.g., Spicy, Vegan).
        - Keep text appetizing, concise, and ready for a menu.
        - End with a closing sentence highlighting why this dish is a must-try.

        Formatting:
        - Output valid HTML using only <p>, <b>, <h1>, <h2>, and <ol>/<li><span> tags for bullet points.
        - Do NOT include any markdown syntax, code fences, or triple backticks.
        - Avoid multiple consecutive <p> tags or empty lines.
        - Return only the HTML content without any commentary.

         IMPORTANT:
        - Only process inputs that are actual food items, beverages, or restaurant meals.
        - If the input is electronics, clothing, gadgets, or anything unrelated to food, respond with only "INVALID_INPUT".
        - If the original input is not meaningful or cannot be converted into a food menu description, respond with only "INVALID_INPUT".
        - Do not return generic explanations, fallback messages, or translations for unrelated items.
        PROMPT;
    }
}

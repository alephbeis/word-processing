<?php

namespace App\Services\WordProcessing;

use App\Services\WordProcessing\CharacterDecompositionRules;

class WordProcessorService
{
    protected $letters;
    protected $nekudos;
    protected $taamim;
    protected $otherGlyphs;

    protected $word;
    protected $wordLetters;
    protected $wordNekudos;
    protected $matchingPattern;

    protected $source;
    protected $hexCharactersArray;
    protected $hexCharactersSplitByLetters;

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $characters = config('constants.CHARECTERS_HEX_CODES');
        $this->letters = $characters['LETTERS'];
        $this->nekudos = $characters['NEKUDOS'];
        $this->taamim = $characters['TAAMIM'];
        $this->otherGlyphs = $characters['OTHER GLYPHS'];
    }

    /**
     * Process word
     * @return Array processed word data
     */
    public function processWord($word, $source = '')
    {
        $this->word = $word;
        $this->source = $source;
        $this->replaceChracters();

        $this->fixOrderOfLetters();
        $this->setFinalWord();

        return [
            'name' => $this->word,
            'letters' => $this->wordLetters,
            'nekudos' => $this->wordNekudos,
            'matching_pattern' => $this->matchingPattern,
            'hexSplitByLetters' => $this->hexCharactersSplitByLetters,
            'source' => $source,
        ];
    }

    protected function replaceChracters()
    {
        /**
         * Decompose Word
         * The function of character Decomposition is to break down ligatures
         * that are a single character that represents two characters.
         * For example, שׁ \u{FB2A}" => שׁ "\u{05E9}\u{05C1}2A.
         * The reason we decompose the ligature to its root characters is
         * so that when we process the words we only need to account for a
         * single option of how that letter can be represented.
         * To ensure each character combination is uniform we preprocess the characters
         * and precomposed ligatures are decomposed to their standard compositions.
         */
        $rulesFrom = array_keys(CharacterDecompositionRules::getRules());
        $rulesTo = array_values(CharacterDecompositionRules::getRules());
        $this->word = str_replace($rulesFrom, $rulesTo, $this->word, $count_replace);

        /* Replace Characters */
        // Replace U+05BA (unique type of cholom) with U+05B9 (standard cholam)
        $this->word = str_replace("\u{05BA}", "\u{05B9}", $this->word, $count_replace);
        // Replace U+05C7 (Kamatz Katan) with U+05B8 (Kamatz)
        $this->word = str_replace("\u{05C7}", "\u{05B8}", $this->word, $count_replace);
    }

    /**
     * Remove unnecessary characters:
     * - Acceptable characters for Tanach: letter, shin/sin dot, dagesh, nekuda, taam, meseg, rafe, makif
     * - Acceptable characters for Non Tanach: letter, shin/sin dot, dagesh, nekuda
     * Fix the order of the letters:
     * - This is done so that characters are always in the same predictable order
     * - Order: letter, shin/sin dot, dagesh, nekuda, taam, other mark (meseg and rafe)
     */
    protected function fixOrderOfLetters()
    {
        $hexCharactersArray = array_map(function ($character) {
            return bin2hex($character);
        }, mb_str_split($this->word));

        $shinDot = '';
        $dagesh = '';
        $nekudah = '';
        $taamim = '';
        $otherMark = '';
        $sortedHexCharacters = [];
        $addLamed = false;

        foreach ($hexCharactersArray as $hexCharacter) {
            if (in_array($hexCharacter, $this->letters)
                || ($hexCharacter === $this->otherGlyphs['Makif'] && !str_contains($this->source, 'Siddur'))
                // for Aleph-Lamed we need to split them up
                || $hexCharacter === 'efad8f') {
                $shinDot ? $sortedHexCharacters[] = $shinDot : null;
                $shinDot = '';
                $dagesh ? $sortedHexCharacters[] = $dagesh : null;
                $dagesh = '';
                $nekudah ? $sortedHexCharacters[] = $nekudah : null;
                $nekudah = '';
                $taamim ? $sortedHexCharacters[] = $taamim : null;
                $taamim = '';
                $otherMark ? $sortedHexCharacters[] = $otherMark : null;
                $otherMark = '';
                if ($addLamed) {
                    $sortedHexCharacters[] = $this->letters['Lamed'];
                    $addLamed = false;
                }

                if ($hexCharacter === 'efad8f') {
                    // if it's aleph lamed add a aleph and a lamed will be saved before the next letter
                    $sortedHexCharacters[] = $this->letters['Aleph'];
                    $addLamed = true;
                } else {
                    $sortedHexCharacters[] = $hexCharacter;
                }
            } elseif (in_array($hexCharacter, [$this->otherGlyphs['Shin dot'], $this->otherGlyphs['Sin dot']])) {
                $shinDot .= $hexCharacter;
            } elseif ($hexCharacter === $this->otherGlyphs['Dagesh']) {
                $dagesh .= $hexCharacter;
            } elseif (in_array($hexCharacter, $this->nekudos)) {
                $nekudah .= $hexCharacter;
            } elseif (in_array($hexCharacter, $this->taamim) && !str_contains($this->source, 'Siddur')) {
                $taamim .= $hexCharacter;
            } elseif ((in_array($hexCharacter, [
                        $this->otherGlyphs['Meseg'],
                        $this->otherGlyphs['Rafe']
                    ]) || (
                        $this->source === 'Tanach Files' &&
                        in_array($hexCharacter, [
                            $this->otherGlyphs['Upper Dot'],
                            $this->otherGlyphs['Lower Dot']
                        ])
                    )) && !str_contains($this->source, 'Siddur')) {
                $otherMark .= $hexCharacter;
            }
        }

        $shinDot ? $sortedHexCharacters[] = $shinDot : null;
        $dagesh ? $sortedHexCharacters[] = $dagesh : null;
        $nekudah ? $sortedHexCharacters[] = $nekudah : null;
        $taamim ? $sortedHexCharacters[] = $taamim : null;
        $otherMark ? $sortedHexCharacters[] = $otherMark : null;
        if ($addLamed) {
            $sortedHexCharacters[] = $this->letters['Lamed'];
            $addLamed = false;
        }

        $this->hexCharactersArray = $sortedHexCharacters;
    }

    protected function setFinalWord()
    {
        $word = '';
        $letters = '';
        $nekudos = '';
        $matchingPattern = '';

        $splitByLetters = [];
        $lastLetterSet = '';

        for ($i = 0; $i < count($this->hexCharactersArray); $i++) {
            $hexCharacter = $this->hexCharactersArray[$i];
            $nextCharacter = $this->hexCharactersArray[$i + 1] ?? null;
            $nextNextCharacter = $this->hexCharactersArray[$i + 2] ?? null;
            $previousCharacter = $this->hexCharactersArray[$i - 1] ?? null;

            $word .= $hexCharacter;
            if (in_array($hexCharacter, $this->nekudos)
                // This is a vav from a CholamMalei
                || $hexCharacter . $nextCharacter === $this->nekudos['CholamMalei']
                // This is a vav from a Shuruk
                || ($hexCharacter . $nextCharacter === $this->nekudos['Shuruk']
                    && !in_array($nextNextCharacter, $this->nekudos))
                // This is a Dagesh from a Shuruk. CholamMalei will get added anyways cause it's a CholamChaser
                || $previousCharacter . $hexCharacter === $this->nekudos['Shuruk']
                // This is a yud and the letter had a chirik, meaning it's a ChirirkMalei
                || ($hexCharacter === $this->letters['Yud']
                    && str_contains($lastLetterSet, $this->nekudos['ChirikChaser']))) {
                $nekudos .= $hexCharacter;
                $matchingPattern .= $hexCharacter;
                $lastLetterSet .= $hexCharacter;
            } elseif (in_array($hexCharacter, $this->letters)) {
                $letters .= $hexCharacter;
                $matchingPattern .= $hexCharacter;

                $lastLetterSet ? $splitByLetters[] = $lastLetterSet : null;
                $lastLetterSet = $hexCharacter;
            } elseif (in_array($hexCharacter, $this->taamim)) {
                $matchingPattern .= 'trup';
                $lastLetterSet .= 'trup';
            } else {
                $matchingPattern .= $hexCharacter;
                $lastLetterSet .= $hexCharacter;
            }
        }
        $splitByLetters[] = $lastLetterSet;

        $this->word = hex2bin($word);
        $this->wordLetters = hex2bin($letters);
        $this->wordNekudos = hex2bin($nekudos);
        $this->matchingPattern = $matchingPattern;
        $this->hexCharactersSplitByLetters = $splitByLetters;
    }
}

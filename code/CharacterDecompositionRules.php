<?php

namespace App\Services\WordProcessing;

class CharacterDecompositionRules
{
    public static function getRules()
    {
        return [
            "\u{FB1D}" => "\u{05D9}\u{05B4}",
            "\u{FB2A}" => "\u{05E9}\u{05C1}",
            "\u{FB2B}" => "\u{05E9}\u{05C2}",
            "\u{FB2C}" => "\u{05E9}\u{05C2}",
            "\u{FB2D}" => "\u{FB49}\u{05C2}",
            "\u{FB2E}" => "\u{05D0}\u{05B7}",
            "\u{FB2F}" => "\u{05D0}\u{05B8}",
            "\u{FB30}" => "\u{05D0}\u{05BC}",
            "\u{FB31}" => "\u{05D1}\u{05BC}",
            "\u{FB32}" => "\u{05D2}\u{05BC}",
            "\u{FB33}" => "\u{05D3}\u{05BC}",
            "\u{FB34}" => "\u{05D4}\u{05BC}",
            "\u{FB35}" => "\u{05D5}\u{05BC}",
            "\u{FB36}" => "\u{05D6}\u{05BC}",
            "\u{FB38}" => "\u{05D8}\u{05BC}",
            "\u{FB39}" => "\u{05D9}\u{05BC}",
            "\u{FB3A}" => "\u{05DA}\u{05BC}",
            "\u{FB3B}" => "\u{05DB}\u{05BC}",
            "\u{FB3C}" => "\u{05DC}\u{05BC}",
            "\u{FB3E}" => "\u{05DE}\u{05BC}",
            "\u{FB40}" => "\u{05E0}\u{05BC}",
            "\u{FB41}" => "\u{05E1}\u{05BC}",
            "\u{FB43}" => "\u{05E3}\u{05BC}",
            "\u{FB44}" => "\u{05E4}\u{05BC}",
            "\u{FB46}" => "\u{05E6}\u{05BC}",
            "\u{FB47}" => "\u{05E7}\u{05BC}",
            "\u{FB48}" => "\u{05E8}\u{05BC}",
            "\u{FB49}" => "\u{05E9}\u{05BC}",
            "\u{FB4A}" => "\u{05EA}\u{05BC}",
            "\u{FB4B}" => "\u{05D5}\u{05B9}",
            "\u{FB4C}" => "\u{05D1}\u{05BF}",
            "\u{FB4D}" => "\u{05DB}\u{05BF}",
            "\u{FB4E}" => "\u{05E4}\u{05BF}",
        ];
    }
}

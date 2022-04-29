# Reference


* [Hebrew Characters →](https://docs.google.com/spreadsheets/d/1uuXn2kW592K6ZOgvgQvsLHQDl0N4mRqdWywqFfR8xHw/edit#gid=1664298110)
* [Skills Processing →](https://docs.google.com/spreadsheets/d/1aGNA9KMmzh65nkApesA_QKnTc84_qWrGfuqo0SP86Rg/edit#gid=883709227)

# 1. Process

Before we import a word we need to process the characters of the word to ensure we have predictable results

## ‣ A. Replace

**Decompose Word**

* The function of character Decomposition is to break down ligatures that are a single character that represents two characters.
* For example, שׁ \\u{FB2A} =\> שׁ \\u{05E9}\\u{05C1}2A.
* The reason we decompose the ligature to its root characters is so that when we process the words we only need to account for a single option of how that letter can be represented. To ensure each character combination is uniform we preprocess the characters and precomposed ligatures are decomposed to their standard compositions.
* When decomposing a aleph lamed, the nekuda needs

**Replace Characters**

* Replace U+05BA with U+05B9
    * U+05BA is a unique type of cholom, we replace all instances with the standard U+05B9
* Replace U+05C7 (Kamatz Katan) with U+05B8 (Kamatz)

## ‣ B. Order & Clean Word

**Remove unnecessary characters**

* Only allowed characters are stored
    * Acceptable characters for Tanach: letter, shin/sin dot, dagesh, nekuda, taam, meseg, rafe, upper dot, lower dot
    * Acceptable characters for Non Tanach: letter, shin/sin dot, dagesh, nekuda

**Fix the order of the letters**

* This is done so that characters are always in the same predictable order
* Order: letter, shin/sin dot, dagesh, nekuda, taam, other mark (meseg, rafe, upper/lower dot)

## ‣ C. Create Matching Pattern

* Once we have a final character string, we create a "matching pattern" using bin2hex
    * For word bank import replace all taamim with `trup`
    * For Tanach import, store all taamim

# 2. Qualify

## ‣ A. Disqualify

A word that matches any of the below criteria is ignored and not imported:

* Word has less than two letters
    * In Hebrew, there are no single letter words, so if the word has only one letter, its not an actual word and is not processed
* Letter with no Nekuda
    * All letters except Aleph, Yud, Vav: is not followed by a Cholam Malei *or* is not the last letter of a word
    * Aleph or Yud: is not followed by a Cholam Malei *or* is not preceded by a letter with a nekuda *or* is not the last letter of a word
    * Vav: is not preceded by a Aleph *or* is not the last letter of a word
* Shin missing a dot
    * A word that has shin that is missing a dot, is excluded.
    * The shin must contain one of the following - shin dot, sin dot, cholam chaser or the preceding letter has a cholam chasar

## ‣ B. Qualify

* Check if the word is unique as per below

### Unique Words

* Below is defined "unique" as it applies to the different imports

**Tanach**

* Words that have a unique  pattern of letter, nekuda, meseg and **placement** of the taamim
* For tammim, we only look for the position of the team, but the actual taam does not matter.
* e.g. two words are the same but have a different taam, but the taam is at the same location, these words are considered the same

**Non Tanach**

* Words that have a unique pattern of letters and nekudos

# 3. Process Skills

* After a word is processed and found to be unique, we check which skills it has as per [this doc →](https://docs.google.com/spreadsheets/d/1aGNA9KMmzh65nkApesA_QKnTc84_qWrGfuqo0SP86Rg/edit#gid=883709227)
* We associate each word with all the skills that this word matches too
* Non Tanach word (e.g. Imported and Siddur) will not be processed for skills that rely on taamim (as hey don't have taamim)
    * Milel, Milra, Kamatz/Katan Gadol, Sheva after Tenua Gedolah/Ketana

# Other

* Source of import and location in the import is recorded
* Source: Sefer or imported
* Location: Chapter and Verse
* e.g. Bereishis, 2:23

<?php

namespace SimplePageCrawler;

use ArrayObject;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\Exception;

class Keywords extends AbstractOptions{

    public function result($html, $p = 0) {
        $string = preg_replace('#<script(.*?)>(.*?)</script>#is', " ", $html);
        $subject = preg_replace('/<[^>]*>/', " ", $string);
        //$subject = preg_replace('/[\s^\n]+/', " ", $string);

        $subject = strtolower($subject);

        if ($p == 2) {
            $pattern = '/\w+[[:space:]]\w+/';
        } elseif ($p == 3) {
            $pattern = '/\w+[[:space:]]\w+[[:space:]]\w+/';
        } else {
            $pattern = '/\w+/';
        }

        preg_match_all($pattern, $subject, $match, PREG_OFFSET_CAPTURE);
        $arr1 = $this->removeStopWords($match[0]);
        array_multisort(array_map('strlen', $arr1), $arr1);

        $arr1 = array_reverse($arr1);

        $keywords = array_count_values($arr1);

        $words_sum = count($arr1);
        $results = array();
        $results [] = array(
            'total words' => $words_sum
        );
        foreach ($keywords as $key => $value) {
            $percent = 100 / $words_sum * $value;
            if (!preg_match("/[0-9]/", trim($key))) {
                $results [] = array(
                    'keyword' => trim($key),
                    'count' => $value,
                    'percent' => round($percent, 2)
                );
            }
        }

        return $results;
    }

    function keywordsArray($html) {
        return array(
            'one' => $this->one_word_keywords($html),
            'two' => $this->two_word_keywords($html),
            'three' => $this->three_word_keywords($html)
        );
    }

    function one_word_keywords($html) {
        $kc = new \SimplePageCrawler\Keywords();

        return array_slice($kc->result($html), 0, 3); 
    }

    function two_word_keywords($html) {
        $kc = new \SimplePageCrawler\Result\Keywords();

        return array_slice($kc->result($html, 2), 0, 3); 
    }

    function three_word_keywords($html) {
        $kc = new \SimplePageCrawler\Result\Keywords();

        return array_slice($kc->result($html, 3), 0, 3); 
    }

    function keywordDisplay($keywords, $tags, $title, $desc) {
        $keywordsConsistency = array();
        unset($keywords[0]);
        
        foreach ($keywords as $key => $keyword) {
            $keyword = strtolower($keyword['keyword']);
            $keywordsConsistency[$keyword]['title'] = strpos(strtolower($title['title']), $keyword) !== false ? 1 : 0;
            $keywordsConsistency[$keyword]['description'] = strpos(strtolower($desc['description']), $keyword) !== false ? 1 : 0;

            $h1_array = isset($tags['h1']) ? $tags['h1'] : array();
            $h2_array = isset($tags['h2']) ? $tags['h2'] : array();
            $h3_array = isset($tags['h3']) ? $tags['h3'] : array();
            $h4_array = isset($tags['h4']) ? $tags['h4'] : array();
            $h5_array = isset($tags['h5']) ? $tags['h5'] : array();
            $h6_array = isset($tags['h6']) ? $tags['h6'] : array();

            $h1 = array_filter($h1_array, function($var) use ($keyword) {
                return preg_match("/\b$keyword\b/i", $var);
            });
            $h2 = array_filter($h2_array, function($var) use ($keyword) {
                return preg_match("/\b$keyword\b/i", $var);
            });
            $h3 = array_filter($h3_array, function($var) use ($keyword) {
                return preg_match("/\b$keyword\b/i", $var);
            });
            $h4 = array_filter($h4_array, function($var) use ($keyword) {
                return preg_match("/\b$keyword\b/i", $var);
            });
            $h5 = array_filter($h5_array, function($var) use ($keyword) {
                return preg_match("/\b$keyword\b/i", $var);
            });
            $h6 = array_filter($h6_array, function($var) use ($keyword) {
                return preg_match("/\b$keyword\b/i", $var);
            });
            $keywordsConsistency[$keyword]['H'][] = (!empty($h1) || !empty($h2) || !empty($h3) || !empty($h4) || !empty($h5) || !empty($h6) ) ? 1 : 0;
            $keywordsConsistency[$keyword]['H'][] = array(
                'h1' => !empty($h1) ? 1 : 0,
                'h2' => !empty($h2) ? 1 : 0,
                'h3' => !empty($h3) ? 1 : 0,
                'h4' => !empty($h4) ? 1 : 0,
                'h5' => !empty($h5) ? 1 : 0,
                'h6' => !empty($h6) ? 1 : 0
            );
        }

        return $keywordsConsistency;
    }

    function removeStopWords($keywords) {
        $arr = array();
        //$stopWords = array("a", "ii", "about", "above", "according", "across", "39", "actually", "ad", "adj", "ae", "af", "after", "afterwards", "ag", "again", "against", "ai", "al", "all", "almost", "alone", "along", "already", "also", "although", "always", "am", "among", "amongst", "an", "and", "another", "any", "anyhow", "anyone", "anything", "anywhere", "ao", "aq", "ar", "are", "aren", "aren't", "around", "arpa", "as", "at", "au", "aw", "az", "b", "ba", "bb", "bd", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "begin", "beginning", "behind", "being", "below", "beside", "besides", "between", "beyond", "bf", "bg", "bh", "bi", "billion", "bj", "bm", "bn", "bo", "both", "br", "bs", "bt", "but", "buy", "bv", "bw", "by", "bz", "c", "ca", "can", "can't", "cannot", "caption", "cc", "cd", "cf", "cg", "ch", "ci", "ck", "cl", "click", "cm", "cn", "co", "co.", "com", "copy", "could", "couldn", "couldn't", "cr", "cs", "cu", "cv", "cx", "cy", "cz", "d", "de", "did", "didn", "didn't", "dj", "dk", "dm", "do", "does", "doesn", "doesn't", "don", "don't", "down", "during", "dz", "e", "each", "ec", "edu", "ee", "eg", "eh", "eight", "eighty", "either", "else", "elsewhere", "end", "ending", "enough", "er", "es", "et", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "f", "few", "fi", "fifty", "find", "first", "five", "fj", "fk", "fm", "fo", "for", "former", "formerly", "forty", "found", "four", "fr", "free", "from", "further", "fx", "g", "ga", "gb", "gd", "ge", "get", "gf", "gg", "gh", "gi", "gl", "gm", "gmt", "gn", "go", "gov", "gp", "gq", "gr", "gs", "gt", "gu", "gw", "gy", "h", "had", "has", "hasn", "hasn't", "have", "haven", "haven't", "he", "he'd", "he'll", "he's", "help", "hence", "her", "here", "here's", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "hk", "hm", "hn", "home", "homepage", "how", "however", "hr", "ht", "htm", "html", "http", "hu", "hundred", "i", "i'd", "i'll", "i'm", "i've", "i.e.", "id", "ie", "if", "il", "im", "in", "inc", "inc.", "indeed", "information", "instead", "int", "into", "io", "iq", "ir", "is", "isn", "isn't", "it", "it's", "its", "itself", "j", "je", "jm", "jo", "join", "jp", "k", "ke", "kg", "kh", "ki", "km", "kn", "kp", "kr", "kw", "ky", "kz", "l", "la", "last", "later", "latter", "lb", "lc", "least", "less", "let", "let's", "li", "like", "likely", "lk", "ll", "lr", "ls", "lt", "ltd", "lu", "lv", "ly", "m", "ma", "made", "make", "makes", "many", "maybe", "mc", "md", "me", "meantime", "meanwhile", "mg", "mh", "microsoft", "might", "mil", "million", "miss", "mk", "ml", "mm", "mn", "mo", "more", "moreover", "most", "mostly", "mp", "mq", "mr", "mrs", "ms", "msie", "mt", "mu", "much", "must", "mv", "mw", "mx", "my", "myself", "mz", "n", "na", "namely", "nc", "ne", "neither", "net", "netscape", "never", "nevertheless", "new", "next", "nf", "ng", "ni", "nine", "ninety", "nl", "no", "nobody", "none", "nonetheless", "noone", "nor", "not", "nothing", "now", "nowhere", "np", "nr", "nu", "nz", "o", "of", "off", "often", "om", "on", "once", "one", "one's", "only", "onto", "or", "org", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "overall", "own", "p", "pa", "page", "pe", "per", "perhaps", "pf", "pg", "ph", "pk", "pl", "pm", "pn", "pr", "pt", "pw", "py", "q", "qa", "r", "rather", "re", "recent", "recently", "reserved", "ring", "ro", "ru", "rw", "s", "sa", "same", "sb", "sc", "sd", "se", "seem", "seemed", "seeming", "seems", "seven", "seventy", "several", "sg", "sh", "she", "she'd", "she'll", "she's", "should", "shouldn", "shouldn't", "si", "since", "site", "six", "sixty", "sj", "sk", "sl", "sm", "sn", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "sr", "st", "still", "stop", "su", "such", "sv", "sy", "sz", "t", "taking", "tc", "td", "ten", "text", "tf", "tg", "test", "th", "than", "that", "that'll", "that's", "the", "their", "them", "themselves", "then", "thence", "there", "there'll", "there's", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "they'd", "they'll", "they're", "they've", "thirty", "this", "those", "though", "thousand", "three", "through", "throughout", "thru", "thus", "tj", "tk", "tm", "tn", "to", "together", "too", "toward", "towards", "tp", "tr", "trillion", "tt", "tv", "tw", "twenty", "two", "tz", "u", "ua", "ug", "uk", "um", "under", "unless", "unlike", "unlikely", "until", "up", "upon", "us", "use", "used", "using", "uy", "uz", "v", "va", "vc", "ve", "very", "vg", "vi", "via", "vn", "vu", "w", "was", "wasn", "wasn't", "we", "we'd", "we'll", "we're", "we've", "web", "webpage", "website", "welcome", "well", "were", "weren", "weren't", "wf", "what", "what'll", "what's", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "who'd", "who'll", "who's", "whoever", "NULL", "whole", "whom", "whomever", "whose", "why", "will", "with", "within", "without", "won", "won't", "would", "wouldn", "wouldn't", "ws", "www", "x", "y", "ye", "yes", "yet", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", "yourselves", "yt", "yu", "z", "za", "zm", "zr", "10", "z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "their", "about", "after", "again", "air", "all", "along", "also", "an", "and", "another", "any", "are", "around", "as", "at", "away", "back", "be", "because", "been", "before", "below", "between", "both", "but", "by", "came", "can", "come", "could", "day", "did", "different", "do", "does", "don't", "down", "each", "end", "even", "every", "few", "find", "first", "for", "found", "from", "get", "give", "go", "good", "great", "had", "has", "have", "he", "help", "her", "here", "him", "his", "home", "house", "how", "I", "if", "in", "into", "is", "it", "its", "just", "know", "large", "last", "left", "like", "line", "little", "long", "look", "made", "make", "man", "many", "may", "me", "men", "might", "more", "most", "Mr.", "must", "my", "name", "never", "new", "next", "no", "not", "now", "number", "of", "off", "old", "on", "one", "only", "or", "other", "our", "out", "over", "own", "part", "people", "place", "put", "read", "right", "said", "same", "saw", "say", "see", "she", "should", "show", "small", "so", "some", "something", "sound", "still", "such", "take", "tell", "than", "that", "the", "them", "then", "there", "these", "they", "thing", "think", "this", "those", "thought", "three", "through", "time", "to", "together", "too", "two", "under", "up", "us", "use", "very", "want", "water", "way", "we", "well", "went", "were", "what", "when", "where", "which", "while", "who", "why", "will", "with", "word", "work", "world", "would", "write", "year", "you", "your", "was");
        $stopWords = array("a", "ii", "about", "above", "according", "across", "39", "actually", "ad", "adj", "ae", "af", "after", "afterwards", "ag", "again", "against", "ai", "al", "all", "almost", "alone", "along", "already", "also", "although", "always", "am", "among", "amongst", "an", "and", "another", "any", "anyhow", "anyone", "anything", "anywhere", "ao", "aq", "ar", "are", "aren", "aren't", "around", "arpa", "as", "at", "au", "aw", "az", "b", "ba", "bb", "bd", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "begin", "beginning", "behind", "being", "below", "beside", "besides", "between", "beyond", "bf", "bg", "bh", "bi", "billion", "bj", "bm", "bn", "bo", "both", "br", "bs", "bt", "but", "buy", "bv", "bw", "by", "bz", "c", "ca", "can", "can't", "cannot", "caption", "cc", "cd", "cf", "cg", "ch", "ci", "ck", "cl", "click", "cm", "cn", "co", "co.", "com", "copy", "could", "couldn", "couldn't", "cr", "cs", "cu", "cv", "cx", "cy", "cz", "d", "de", "did", "didn", "didn't", "dj", "dk", "dm", "do", "does", "doesn", "doesn't", "don", "don't", "down", "during", "dz", "e", "each", "ec", "edu", "ee", "eg", "eh", "eight", "eighty", "either", "else", "elsewhere", "end", "ending", "enough", "er", "es", "et", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "f", "few", "fi", "fifty", "find", "first", "five", "fj", "fk", "fm", "fo", "for", "former", "formerly", "forty", "found", "four", "fr", "free", "from", "further", "fx", "g", "ga", "gb", "gd", "ge", "get", "gf", "gg", "gh", "gi", "gl", "gm", "gmt", "gn", "go", "gov", "gp", "gq", "gr", "gs", "gt", "gu", "gw", "gy", "h", "had", "has", "hasn", "hasn't", "have", "haven", "haven't", "he", "he'd", "he'll", "he's", "help", "hence", "her", "here", "here's", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "hk", "hm", "hn", "home", "homepage", "how", "however", "hr", "ht", "htm", "html", "http", "hu", "hundred", "i", "i'd", "i'll", "i'm", "i've", "i.e.", "id", "ie", "if", "il", "im", "in", "inc", "inc.", "indeed", "information", "instead", "int", "into", "io", "iq", "ir", "is", "isn", "isn't", "it", "it's", "its", "itself", "j", "je", "jm", "jo", "join", "jp", "k", "ke", "kg", "kh", "ki", "km", "kn", "kp", "kr", "kw", "ky", "kz", "l", "la", "last", "later", "latter", "lb", "lc", "least", "less", "let", "let's", "li", "like", "likely", "lk", "ll", "lr", "ls", "lt", "ltd", "lu", "lv", "ly", "m", "ma", "made", "make", "makes", "many", "maybe", "mc", "md", "me", "meantime", "meanwhile", "mg", "mh", "microsoft", "might", "mil", "million", "miss", "mk", "ml", "mm", "mn", "mo", "more", "moreover", "most", "mostly", "mp", "mq", "mr", "mrs", "ms", "msie", "mt", "mu", "much", "must", "mv", "mw", "mx", "my", "myself", "mz", "n", "na", "namely", "nc", "ne", "neither", "net", "netscape", "never", "nevertheless", "new", "next", "nf", "ng", "ni", "nine", "ninety", "nl", "no", "nobody", "none", "nonetheless", "noone", "nor", "not", "nothing", "now", "nowhere", "np", "nr", "nu", "nz", "o", "of", "off", "often", "om", "on", "once", "one", "one's", "only", "onto", "or", "org", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "overall", "own", "p", "pa", "page", "pe", "per", "perhaps", "pf", "pg", "ph", "pk", "pl", "pm", "pn", "pr", "pt", "pw", "py", "q", "qa", "r", "rather", "re", "recent", "recently", "reserved", "ring", "ro", "ru", "rw", "s", "sa", "same", "sb", "sc", "sd", "se", "seem", "seemed", "seeming", "seems", "seven", "seventy", "several", "sg", "sh", "she", "she'd", "she'll", "she's", "should", "shouldn", "shouldn't", "si", "since", "site", "six", "sixty", "sj", "sk", "sl", "sm", "sn", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "sr", "st", "still", "stop", "su", "such", "sv", "sy", "sz", "t", "taking", "tc", "td", "ten", "text", "tf", "tg", "test", "th", "than", "that", "that'll", "that's", "the", "their", "them", "themselves", "then", "thence", "there", "there'll", "there's", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "they'd", "they'll", "they're", "they've", "thirty", "this", "those", "though", "thousand", "three", "through", "throughout", "thru", "thus", "tj", "tk", "tm", "tn", "to", "together", "too", "toward", "towards", "tp", "tr", "trillion", "tt", "tv", "tw", "twenty", "two", "tz", "u", "ua", "ug", "uk", "um", "under", "unless", "unlike", "unlikely", "until", "up", "upon", "us", "use", "used", "using", "uy", "uz", "v", "va", "vc", "ve", "very", "vg", "vi", "via", "vn", "vu", "w", "was", "wasn", "wasn't", "we", "we'd", "we'll", "we're", "we've", "web", "webpage", "website", "welcome", "well", "were", "weren", "weren't", "wf", "what", "what'll", "what's", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "who'd", "who'll", "who's", "whoever", "NULL", "whole", "whom", "whomever", "whose", "why", "will", "with", "within", "without", "won", "won't", "would", "wouldn", "wouldn't", "ws", "www", "x", "y", "ye", "yes", "yet", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", "yourselves", "yt", "yu", "z", "za", "zm", "zr", "10", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "air", "away", "back", "came", "come", "day", "different", "give", "good", "great", "house", "I", "just", "know", "large", "left", "line", "little", "long", "look", "man", "may", "men", "Mr.", "name", "number", "old", "part", "people", "place", "put", "read", "right", "said", "saw", "say", "see", "show", "small", "sound", "take", "tell", "thing", "think", "thought", "time", "want", "water", "way", "went", "word", "work", "world", "write", "year");
        foreach ($keywords as $key => $value) {
            if (!$this->strposa($value[0], $stopWords)) {
                $arr[] = $value[0];
            }
        }

        return $arr; //array_filter(array_unique($arr));
    }

    function strposa($haystack, $needle, $offset = 0) {
        if (!is_array($needle)) {
            $needle = array($needle);
        }
        foreach ($needle as $query) {
            $pattern = "/\b$query\b/i";
            preg_match($pattern, $haystack, $matches, PREG_OFFSET_CAPTURE);
            if (!empty($matches)) {
                return true;
            }
        }

        return false;
    }

}

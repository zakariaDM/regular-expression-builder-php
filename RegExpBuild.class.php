<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RegExpBuild
 *
 * @author Zakaria
 */
class RegExpBuild {

    private $type;
    private $operation;
    private $value;
    private $value2;

    const TYPE_NUMBER = "number";
    const TYPE_TEXT = "text";
    const TYPE_PATERN = "patern";
    const NUMBER_GREATER_THAN = "greater_than";
    const NUMBER_GREATER_THAN_OR_EQUAL = "greater_than_or_equal_to";
    const NUMBER_LESS_THAN = "less_than";
    const NUMBER_LESS_THAN_OR_EQUAL = "less_than_or_equal_to";
    const NUMBER_NOT_EQUAL_TO = "not_equal_to";
    const NUMBER_EQUAL_TO = "equal_to";
    const NUMBER_BETWEEN = "between";
    const NUMBER_NOT_BETWEEN = "not_between";
    const NUMBER_IS_NUMBER = "is_number";
    const NUMBER_WHOLE_NUMBER = "whole_number";
    const TEXT_CONTAINS = "contains";
    const TEXT_DOES_NOT_CONTAIN = "does_not_contain";
    const TEXT_EMAIL = "email";
    const TEXT_URL = "url";

    function __construct($type, $operation, $value, $value2 = NULL) {
        $this->type = $type;
        $this->operation = $operation;
        $this->value = $value;

        if ($value2 != NULL) {
            $this->value2 = $value2;
        }

        if ($this->type == self::TYPE_NUMBER) {
            $this->operation = str_replace(" ", "", $this->operation);
        }
    }

    /**
     * retourn l'expression reguliere de la condition "less than or equal" d'un chiffre entier positive
     * @return string
     */
    public function build_number_patern_less_Than_or_equal() {
        $number_lenght = strlen($this->value);
        $value_calc = $this->value;
// 576 : 0 - 99  ou  100 - 499  ou  499  ou  500 - 576
        $valueDec = (((int) ($value_calc / (pow(10, ($number_lenght - 1))))) * (pow(10, ($number_lenght - 1)))) - 1;
//pour les chiffres qui ont une taile inférieur ex: pour le chiffre 9000, on considère les chiffre 1, 34, 543...
        $pattern_part_1 = "^[0-9]{0," . ($number_lenght - 1) . "}";
//100 - 499
        $pattern_part_3 = "";
        for ($i = 0; $i < $number_lenght; $i++) {
            $chiffre = $valueDec % 10;
            $valueDec = (int) ($valueDec / 10);
            $pattern_part_3 = "[0-" . $chiffre . "]" . $pattern_part_3;
        }
//la condition sur le chiffre
//500 - 576
        $array_pattern = array();
        for ($i = 0; $i < $number_lenght - 2; $i++) {
            $array_pattern[] = "";
        }

        $pattern_part_2 = "";
        for ($i = 0; $i < $number_lenght; $i++) {
            $chiffre = $value_calc % 10;
            $value_calc = (int) ($value_calc / 10);
            if ($i == ($number_lenght - 1)) {
                $pattern_part_2 = "[" . $chiffre . "-" . $chiffre . "]" . $pattern_part_2;
                for ($j = 0; $j < count($array_pattern); $j++) {
                    $array_pattern[$j] = "[" . $chiffre . "-" . $chiffre . "]" . $array_pattern[$j];
                }
            } else {
                $pattern_part_2 = "[0-" . $chiffre . "]" . $pattern_part_2;
                if ($i == 0) {
                    for ($j = 0; $j < count($array_pattern); $j++) {
                        $array_pattern[$j] = "[0-9]" . $array_pattern[$j];
                    }
                } else {

                    for ($j = 0; $j < count($array_pattern); $j++) {
                        if ($i == ($j + 1)) {
                            $chiffreToPut = $chiffre - 1;
                            $array_pattern[$j] = "[0-$chiffreToPut]" . $array_pattern[$j];
                        } else {
                            if ($i > ($j + 1)) {
                                $array_pattern[$j] = "[0-$chiffre]" . $array_pattern[$j];
                            } else {
                                $array_pattern[$j] = "[0-9]" . $array_pattern[$j];
                            }
                        }
                    }
                }
            }
        }
        $pattern_part_2_p = "";
        foreach ($array_pattern as $patternItr):
            $pattern_part_2_p = $pattern_part_2_p . $patternItr . "|";
        endforeach;
        return $pattern_part_2 . "|" . $pattern_part_2_p . $pattern_part_3 . "|" . $pattern_part_1;
    }

    /**
     * retourne l'expression de la condition ">=" d'un nombre entier positif
     * @return String
     */
    public function build_number_patern_greater_Than_or_equal() {
        $number_lenght = strlen($this->value);
        $value_calc = $this->value;

//pour les chiffres qui ont une taille inférieur ex: pour le chiffre 9000, on considère les chiffre 1, 34, 543...
        $valueDec = (((int) ($value_calc / (pow(10, ($number_lenght - 1)))) + 1) * (pow(10, ($number_lenght - 1))));

        $pattern_part_1 = "^[0-9]{" . ($number_lenght + 1) . ",100}";

        $pattern_part_3 = "";
        for ($i = 0; $i < $number_lenght; $i++) {
            $chiffre = $valueDec % 10;
            $valueDec = (int) ($valueDec / 10);
            $pattern_part_3 = "[" . $chiffre . "-9]" . $pattern_part_3;
        }

        $array_pattern = array();
        for ($i = 0; $i < $number_lenght - 2; $i++) {
            $array_pattern[] = "";
        }

//la condition sur le chiffre
        $pattern_part_2 = "";
        for ($i = 0; $i < $number_lenght; $i++) {
            $chiffre = $value_calc % 10;
            $value_calc = (int) ($value_calc / 10);
            if ($i == ($number_lenght - 1)) {
                $pattern_part_2 = "[" . $chiffre . "-" . $chiffre . "]" . $pattern_part_2;
                for ($j = 0; $j < count($array_pattern); $j++) {
                    $array_pattern[$j] = "[" . $chiffre . "-" . $chiffre . "]" . $array_pattern[$j];
                }
            } else {
                $pattern_part_2 = "[" . $chiffre . "-9]" . $pattern_part_2;
                if ($i == 0) {
                    for ($j = 0; $j < count($array_pattern); $j++) {
                        $array_pattern[$j] = "[0-9]" . $array_pattern[$j];
                    }
                } else {
                    for ($j = 0; $j < count($array_pattern); $j++) {
                        if ($i == ($j + 1)) {
                            $chiffreToPut = $chiffre + 1;
                            $array_pattern[$j] = "[$chiffreToPut-9]" . $array_pattern[$j];
                        } else {
                            if ($i > ($j + 1)) {
                                $array_pattern[$j] = "[$chiffre-9]" . $array_pattern[$j];
                            } else {
                                $array_pattern[$j] = "[0-9]" . $array_pattern[$j];
                            }
                        }
                    }
                }
            }
        }
        $pattern_part_2_p = "";
        foreach ($array_pattern as $patternItr):
            $pattern_part_2_p = $pattern_part_2_p . $patternItr . "|";
        endforeach;
        return $pattern_part_1 . "|" . $pattern_part_2_p . "|^" . $pattern_part_2 . "|^" . $pattern_part_3;
    }

    public function build_number_patern_greater_than_strict() {
        $greaterThan = new RegExpBuild(self::TYPE_NUMBER, self::NUMBER_GREATER_THAN, ($this->value + 1));
        return $greaterThan->build_number_patern_greater_Than_or_equal();
    }

    public function build_number_patern_less_than_strict() {
        $less_than = new RegExpBuild(self::TYPE_NUMBER, self::NUMBER_LESS_THAN, ($this->value - 1));
        return $less_than->build_number_patern_less_Than_or_equal();
    }

    public function build_number_between_1() {

        $number_lenght_1 = strlen($this->value);
        $value_calc_1 = $this->value;

        $number_lenght_2 = strlen($this->value2);
        $value_calc_2 = $this->value2;

        if ($number_lenght_1 < $number_lenght_2) {
            $valueDec = (((int) ($value_calc_1 / (pow(10, ($number_lenght_1 - 1)))) + 1) * (pow(10, ($number_lenght_1 - 1))));

            $pattern_part_3 = "";
            for ($i = 0; $i < $number_lenght_1; $i++) {
                $chiffre = $valueDec % 10;
                $valueDec = (int) ($valueDec / 10);
                $pattern_part_3 = "[" . $chiffre . "-9]" . $pattern_part_3;
            }
            $pattern_part_2 = "";
            for ($i = 0; $i < $number_lenght_1; $i++) {
                $chiffre = $value_calc_1 % 10;
                $value_calc_1 = (int) ($value_calc_1 / 10);
                if ($i == ($number_lenght_1 - 1)) {
                    $pattern_part_2 = "[" . $chiffre . "-" . $chiffre . "]" . $pattern_part_2;
                } else {
                    $pattern_part_2 = "[" . $chiffre . "-9]" . $pattern_part_2;
                }
            }

            if ($number_lenght_2 - $number_lenght_1 > 1) {
                $pattern_part_1 = "^[0-9]{" . ($number_lenght + 1) . ",100}";
            }
        }
        return $pattern_part_1 . "|^" . $pattern_part_2 . "|^" . $pattern_part_3;
    }

    /**
     * retourne l'expression de la condition diffrent à
     * @return String
     */
    public function build_number_not_equal() {
        $build_greather = new RegExpBuild(self::TYPE_NUMBER, self::NUMBER_GREATER_THAN, $this->value);
        $pattern_part_greather = $build_greather->build_number_patern_greater_Than_or_equal();

        $build_less = new RegExpBuild(self::TYPE_NUMBER, self::NUMBER_LESS_THAN, $this->value);
        $pattern_part_less = $build_greather->build_number_patern_less_than_strict();

        return "(" . $pattern_part_greather . ")" . "|" . "(" . $pattern_part_less . ")";
    }

    /**
     * retourne l'expression reguliere de la condtion "égale"
     * @return String
     */
    public function build_number_equal() {
        return "" . $this->value;
    }

    public function build_is_number() {
        return "[1-9][0-9]+";
    }

    /**
     * retourne l'expression regulière de la condition "entre deux chiffres"
     * @return type
     */
    public function build_number_between() {
        $build_between_inf = new RegExpBuild($this->type, self::NUMBER_LESS_THAN_OR_EQUAL, $this->value2);
        $build_between_sup = new RegExpBuild($this->type, self::NUMBER_GREATER_THAN_OR_EQUAL, $this->value);

        $pattern_between_inf = $build_between_inf->build_number_patern_less_Than_or_equal();
        $pattern_between_sup = $build_between_sup->build_number_patern_greater_Than_or_equal();

        return "(" . $pattern_between_sup . ")" . "-&-" . "(" . $pattern_between_inf . ")";
    }

    /**
     * retourne l'expression regulière de l'expression "inférieur à x et supérieur à y"
     */
    public function build_number_not_between() {
        $build_between_inf = new RegExpBuild(self::TYPE_NUMBER, self::NUMBER_LESS_THAN, $this->value);
        $build_between_sup = new RegExpBuild(self::TYPE_NUMBER, self::NUMBER_GREATER_THAN, $this->value2);

        $pattern_between_inf = $build_between_inf->build_number_patern_less_than_strict();
        $pattern_between_sup = $build_between_sup->build_number_patern_greater_than_strict();

        return "(" . $pattern_between_sup . ")" . "-&-" . "(" . $pattern_between_inf . ")";
    }

    public function build_text_pattern_contains() {
        return "(\b)+" . $this->value . "(\b)*|" . $this->value . "\b|" . $this->value;
    }

    public function build_text_patern_does_not_contain() {
        return "^((?! " . $this->value . " ).)*$";
    }

    public function build_text_pattern_email() {
        return "^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$";
    }

    public function build_text_pattern_url() {
        return "^(https?:\/\/)?((([a-z\d]([a-z\d-]*[a-z\d])*)\.)+[a-z]{2,}|((\d{1,3}\.){3}\d{1,3}))(\:\d+)?(\/[-a-z\d%_.~+]*)*(\\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$";
    }

    public function getRegExp() {
        switch ($this->operation) {
            case self::NUMBER_GREATER_THAN :
                return $this->build_number_patern_greater_than_strict();

            case self::NUMBER_GREATER_THAN_OR_EQUAL :
                return $this->build_number_patern_greater_Than_or_equal();

            case self::NUMBER_LESS_THAN :
                return $this->build_number_patern_less_than_strict();

            case self::NUMBER_LESS_THAN_OR_EQUAL:
                return $this->build_number_patern_less_Than_or_equal();

            case self::NUMBER_BETWEEN:
                return $this->build_number_between();

            case self::NUMBER_NOT_BETWEEN :
                return $this->build_number_not_between();

            case self::NUMBER_NOT_EQUAL_TO :
                return $this->build_number_not_equal();

            case self::NUMBER_EQUAL_TO :
                return $this->build_number_equal();

            case self::TEXT_CONTAINS :
                return $this->build_text_pattern_contains();

            case self::TEXT_DOES_NOT_CONTAIN :
                return $this->build_text_patern_does_not_contain();

            case self::TEXT_EMAIL :
                return $this->build_text_pattern_email();

            case self::TEXT_URL :
                return $this->build_text_pattern_url();

            default:
                return "there is no oretation with this name";
        }
    }

}

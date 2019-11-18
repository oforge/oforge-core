<?php

namespace Insertion\Services;

class InsertionValidationService {

    /*
     * I'll tell you wat I want, what I really really want!
     * Then tell my what you want, what you really really want!
     * I wanna huh, I wanna huh, I wanna huh, I wanna huh,
     * I wanna Middlemiddlemiddleware to validate the form!
     */
    public function titleExists() {
        if (Oforge()->View()->has('data')) {
            $data = Oforge()->View()->get('data');
            return isset($data['insertion_title']) && !empty($data['insertion_title']);
        }
    }
}

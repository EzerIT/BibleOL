<?php
// Copyright © 2013 by Ezer IT Consulting. All rights reserved. Email: claus@ezer.dk

// The DataException and DataException2 classes are identical.
// They are used to signal problems with processing data. By having two
// exceptions, different catch-clauses can be activated.

class DataException extends Exception {
    public function __construct($message) {
        parent::__construct($message);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

class DataException2 extends Exception {
    public function __construct($message) {
        parent::__construct($message);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
?>
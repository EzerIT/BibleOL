<?php

// Renames internal lexeme icon names to names in the fontawesome og bolicon set

class L_icon {
    private static $map = array(
        'l-icon-link'    => 'fas fa-link',
        'l-icon-file'    => 'fas fa-file',
        'l-icon-music'   => 'fas fa-music',
        'l-icon-picture' => 'fas fa-file-image',
        'l-icon-film'    => 'fas fa-film',
        'l-icon-speaker' => 'fas fa-volume-down',
        'l-icon-book'    => 'fas fa-book',
        'l-icon-globe'   => 'fas fa-globe-africa',
        'l-icon-logos'   => 'bolicon bolicon-logos',
        'l-icon-default' => 'fas fa-link',
        );

    public static function css_class(string $icon) {
        return isset(self::$map[$icon]) ? self::$map[$icon] : self::$map['l-icon-default'];
    }

    public static function json() {
        return json_encode(self::$map);
    }
  }


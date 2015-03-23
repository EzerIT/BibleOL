// -*- js -*-

interface Detector {
    detect(font : string): boolean;
}

declare var Detector : new(lang : string) => Detector;

<?php

//Huolehtii meemien tietokantatoiminnallisuudesta.
class Meme extends BaseModel {

    public $id, $poster, $title, $type, $content;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    //Hakee kaikki meemit kannasta. Tukee sivutusta eli hakee haluttu määrä (10)
    //kerrallaan.
    public static function find_all($offset) {
        $query = DB::connection()->prepare('SELECT * FROM Meme ORDER BY id DESC OFFSET :offset LIMIT :limit');
        $query->execute(array('offset' => $offset, 'limit' => self::$entries_per_page));

        return self::fetchMany($query);
    }

    //Toimii muuten kuten yllä oleva, mutta ottaa parametreiksi kolumnin nimen
    //ja etsittävän fraasin hakutoimintoa varten. Tyypillä tarkoitetaan siis
    //Hakutyyppiä tässä kontekstissa. Postaajalla hakeminen on eksakti.
    public static function find_all_by_x($offset, $type, $phrase) {
        $type = self::sanitize_research_type($type);
        if ($type != 'Poster') {
            $phrase = '%' . $phrase . '%';
        }
        $query = DB::connection()->prepare("SELECT * FROM Meme WHERE lower($type) LIKE lower(:phrase) ORDER BY id DESC OFFSET :offset LIMIT :limit");
        $query->execute(array('phrase' => $phrase, 'offset' => $offset, 'limit' => self::$entries_per_page));

        return self::fetchMany($query);
    }

    //Apumetodi, joka kerää kyselyn tulokset, ja palauttaa ne olioina.
    private static function fetchMany($query) {
        $rows = $query->fetchAll();

        return self::construct_from_rows($rows);
    }

    //Apumetodi, joka rakentaa kyselyn tuluoksista meemi-olioita.
    //Käytetään myös FavouriteControllerissa, kun haetaan suosikkeja.
    public static function construct_from_rows($rows) {
        $memes = array();
        foreach ($rows as $row) {
            $memes[] = new Meme(array(
                'id' => $row['id'],
                'poster' => $row['poster'],
                'title' => $row['title'],
                'type' => $row['type'],
                'content' => $row['content'],
            ));
        }
        return $memes;
    }

    //Sanitoi hakutyypin, koska kolumnien nimiä ei voi syöttää parametreinä
    //kyselyihin.
    private static function sanitize_research_type($type) {
        if ($type != 'Title' && $type != 'Type' && $type != 'Content' && $type != 'Poster') {
            $type = 'Title';
        }

        return $type;
    }

    //Etsii yksittäisen meemin kannasta.
    public static function find_one($id) {
        $query = DB::connection()->prepare('SELECT * FROM Meme WHERE id = :id');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $meme = new Meme(array(
                'id' => $row['id'],
                'poster' => $row['poster'],
                'title' => $row['title'],
                'type' => $row['type'],
                'content' => $row['content']
            ));

            return $meme;
        }

        return null;
    }

    //Laskee meeminen kokonaismäärän. Käytetään sivutuksen tuottamisessa.
    public static function count() {
        $query = DB::connection()->prepare('SELECT count(*) as count FROM Meme');
        $query->execute();
        $result = $query->fetch();

        return $result['count'];
    }

    //Laskee hakukyselyn kaikki mahdolliset tulokset sivutusta varten.
    public static function count_search_results($type, $phrase) {
        $type = self::sanitize_research_type($type);
        if ($type != 'Poster') {
            $phrase = '%' . $phrase . '%';
        }
        $query = DB::connection()->prepare("SELECT count(*) AS count FROM Meme WHERE lower($type) LIKE lower(:phrase)");
        $query->execute(array('phrase' => $phrase));
        $result = $query->fetch();

        return $result['count'];
    }

    //Tallettaa meemin kantaan.
    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Meme (poster, title, type, content) VALUES (:poster, :title, :type, :content) RETURNING id');
        $query->execute(array('poster' => $this->poster, 'title' => $this->title, 'type' => $this->type, 'content' => $this->content));
        $result = $query->fetch();
        $this->id = $result['id'];
    }

    //Päivittää kannassa olevan meemin.
    public function update() {
        $query = DB::connection()->prepare('UPDATE Meme SET title = :title, content = :content WHERE id = :id');
        $query->execute(array('title' => $this->title, 'content' => $this->content, 'id' => $this->id));
    }

    //Poistaa meemin kannasta.
    public function delete() {
        $query = DB::connection()->prepare('DELETE FROM Meme WHERE id = :id');
        $query->execute(array('id' => $this->id));
    }

    //Hakee kaikkien kannassa olevien meemien id:t etusivun random meemi
    //-toiminnallisuutta varten.
    public static function find_all_ids() {
        $query = DB::connection()->prepare('SELECT id FROM Meme');
        $query->execute();

        return $query->fetchAll();
    }

    //Lisää meemeihin liittyvät validointisäännöt.
    protected function add_valitron_rules() {
        $this->valitron->rule('required', array('poster', 'title', 'type', 'content'));
        $this->valitron->rule('lengthBetween', 'title', 2, 50);
        $this->valitron->rule('lengthBetween', 'content', 2, 5000);
        $this->valitron->addRule('typeIsValid', function($field, $value, array $params, array $fields) {
            if ($value !== "Copypasta" && $value !== "Image" && $value !== "Video") {
                return false;
            }
            return true;
        }, 'is not valid');
        $this->valitron->rule('typeIsValid', 'type')->message('Just no');

        if ($this->type == 'Video') {
            $this->handle_video_rules();
        } elseif ($this->type == 'Image') {
            $this->handle_image_rules();
        }
    }

    //Apumetodi, joka lisää kuva tyyppisille meemeille oman säännöstönsä.
    private function handle_image_rules() {
        $this->valitron->rule('urlActive', 'content');
        //Tarkistetaan onko annetussa osoitteessa oikeasti kuva.
        $this->valitron->addRule('urlImage', function($field, $value, array $params, array $fields) {
            try {
                $headers = get_headers($value);
                foreach ($headers as $header) {
                    if (strpos($header, 'Content-Type: image/') !== false) {
                        return true;
                    }
                }
            } catch (Exception $ex) {
                
            }
            return false;
        }, 'must be an url of an image');
        $this->valitron->rule('urlImage', 'content');
    }

    //Apumetodi, joka lisää video tyyppisille meemeille oman säännöstönsä.
    private function handle_video_rules() {
        //Tarkistaa onko annettu videoid olemassa. Käytetään vähän erikoisempaa
        //tapaa, koska YouTube ei suoraan anna 404:sta tai vastaavaa
        //virheellisellä id:llä.
        $this->valitron->addRule('videoId', function($field, $value, array $params, array $fields) {
            try {
                $result = file_get_contents("https://www.youtube.com/oembed?url=http%3A//www.youtube.com/watch%3Fv%3D$value&format=json");
            } catch (Exception $ex) {
                
            }
            if (isset($result)) {
                return true;
            }
            return false;
        }, 'must be a valid video id');
        $this->valitron->rule('videoId', 'content');
    }

    //Alustaa validointiolion.
    protected function setup_valitron() {
        $attributes = array('poster' => $this->poster,
            'title' => $this->title,
            'type' => $this->type,
            'content' => $this->content);
        $this->valitron = new Valitron\Validator($attributes);
    }

}

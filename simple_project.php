<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Diyorbek's BookShop Project</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            background: #e4e5e6;
            color: #333;
        }
        .table {
            table-layout: fixed;
            border-collapse: collapse;
            width: 400px;
            margin: 40px auto;
        }

        .table caption {
            margin-bottom: 20px;
            font-weight: bold;
            color: #e91051;
        }

        .table tr {
            display: table-row;
        }

        .table th {
            color: #189c7b;
        }

        .table th,
        .table td {
            border: solid 1px #ccc;
            padding: 10px 20px;
        }

        @media (min-width: 320px) {
            .table {
                width: 300px;
                margin: 20px auto;
            }
    </style>
</head>
<body>
<?php
/* ==============================================================
                         Traits
===============================================================*/
trait Preview {
    public function preview() {
        echo "Preview method was called";
    }
}

trait Reserve {
    public function reserve() {
        echo "Reserve method was called";
    }
}

/* ==============================================================
                       Interfaces
===============================================================*/
interface iGoods {
    public function info();
    public function setPrice($price);
    public function getPrice($discount);
}

interface iGoodsSub {
    public function setSubscriptionPrice($subscriptionPrice);
    public function getSubscriptionPrice($discount);
}

/* ==============================================================
                       Structure
===============================================================*/
class Goods implements iGoods {
    public $title;
    private $price;

    public function info() {
        $data = array_reverse(get_object_vars($this));
        $className = get_class($this);
        $start = "<table class='table'><caption>Goods: $className</caption>" .
            "<tr><th>Specification</th><th>Value</th></tr>";
        $end = "</table>\n";
        $body = "";
        foreach ($data as $key => $value) {
            if (!$value) continue;
            if (gettype($value) == "object") {
                foreach ($value as $spec => $val) {
                    $body .= $this->getLine($spec, $val);
                }
            } elseif (gettype($value) == "array") {
                $body .= $this->getLine($key, implode(", ", $value));
            } else {
                $body .= $this->getLine($key, $value);
            }
        }
        echo $start . $body . $end;
    }

    public function construct($title, $price, $subscriptionPrice) {
        $this->title = $title;
        $this->price = $price;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function getPrice($discount) {
        return $this->price - $this->Discount($this->price, $discount);
    }

    private function getLine($key, $value) {
        return "<tr><td>$key</td><td>$value</td></tr>";
    }

    protected function Discount($amount, $discount) {
        return $amount / 100 * $discount;
    }
}

class Reads extends Goods {
    public $pages, $publisher, $year;
}

class JournalsSub extends Reads implements iGoodsSub {
    private $subscriptionPrice;
    public $number, $numsPerYear;

    public function construct($title, $price, $subscriptionPrice) {
        parent::__construct($title, $price, $subscriptionPrice);
        $this->subscriptionPrice = $subscriptionPrice;
    }

    public function setSubscriptionPrice($subscriptionPrice) {
        $this->subscriptionPrice = $subscriptionPrice;
    }

    public function getSubscriptionPrice($discount) {
        if ($this->subscriptionPrice){
            return $this->subscriptionPrice - $this->Discount($this->subscriptionPrice, $discount);
        }else {
            return $this->numsPerYear * $this->getPrice($discount);
        }
    }
}

class PostCal extends Goods {
    public $size, $type;
}

class CardStamp extends Goods {
    public $country, $series, $size;
}

class Size
{
    public $width, $height;

    public function construct($width, $height) {
        $this->width  = $width;
        $this->height = $height;
    }
}

/* ==============================================================
                       Classes
===============================================================*/
class Book extends Reads
{
    public $author, $hardcover, $genres, $size;
    use Reserve;

    public function construct($title, $price, $subscriptionPrice)
    {
        parent::__construct($title, $price, $subscriptionPrice);
    }

    public function setGenres($genres) {
        $this->genres = gettype($genres) == "array" ? $genres : array($genres);
    }

    public function getGenres() {
        return $this->genres ?  implode(", ", $this->genres) : "";
    }
}

class EBook extends Reads
{
    use Preview;
    public $author, $fileSize;

    public function construct($title, $price, $subscriptionPrice)
    {
        parent::construct($title, $price, $subscriptionPrice);
    }
}

class Magazine extends JournalsSub
{
    use Reserve;
    public $size;

    public function construct($title, $price, $subscriptionPrice)
    {
        parent::construct($title, $price, $subscriptionPrice);
    }
}

class EMagazine extends JournalsSub
{
    use Preview;
    public $fileSize;

    public function construct($title, $price, $subscriptionPrice)
    {
        parent::construct($title, $price, $subscriptionPrice);
    }
}

class Calendar extends PostCal
{
    public $year;

    public function construct($title, $price, $subscriptionPrice)
    {
        parent::construct($title, $price, $subscriptionPrice);
    }
}

class Newspaper extends JournalsSub
{
    public $size, $isColor;
}

class Postcard extends CardStamp
{
    public function construct($title, $price, $subscriptionPrice)
    {
        parent::construct($title, $price, $subscriptionPrice);
    }
}

class Poster extends PostCal
{
    public $series;

    public function customize() {
        echo "Customize method was called";
    }
}

class PostStamp extends CardStamp
{
    public $denomination;
}

/* ==============================================================
                       Output
===============================================================*/
$book = new Book("Homo Deus", 215);
$book->year = 2018;
$book->publisher = "Book Cheef";
$book->pages = 536;
$book->hardcover = "true";
$book->author = "Noah Harari";
$book->setGenres("Non-fiction");
$book->size = new Size(30, 15);
$book->info();

$magazine = new Magazine("Shpil", 50, "450");
$magazine->year = 2018;
$magazine->publisher = "Sharp Wign";
$magazine->pages = 45;
$magazine->numsPerYear = 12;
$magazine->number = 9;
$magazine->size = new Size(40, 20);
$magazine->info();

$newspaper = new Newspaper("Komsomolskaya Pravda", 666, 1337);
$newspaper->year = 1954;
$newspaper->publisher = "Kreml";
$newspaper->pages = 47;
$newspaper->numsPerYear = 48;
$newspaper->number = 36;
$newspaper->isColor = "False";
$newspaper->size = new Size(50, 25);
$newspaper->info();

$ebook = new EBook("Viy", 78);
$ebook->year = 2015;
$ebook->publisher = "Astana";
$ebook->pages = 456;
$ebook->author = "Nikolai Gogol";
$ebook->fileSize = "4.2 Mb";
$ebook->info();

$emagazine = new EMagazine("New York Times", 200, 4000);
$emagazine->year = 2018;
$emagazine->publisher = "BBC";
$emagazine->pages = 50;
$emagazine->numsPerYear = 36;
$emagazine->number = 28;
$emagazine->fileSize = "2.8 Mb";
$emagazine->info();

$postcard = new Postcard("Vienna", 20);
$postcard->country = "France";
$postcard->series = 21; 
$postcard->info();
 
$postStamp = new PostStamp("Kreml", 6);
$postStamp->country = "Russia";
$postStamp->series = 47; 
$postStamp->denomination = 2; 
$postStamp->size = new Size(4, 4); 
$postStamp->info(); 
 
$poster = new Poster("Glinomes", 50);
$poster->type = "Vintage";
$poster->series = 17; 
$poster->size = new Size(72, 72); 
$poster->info(); 
 
$calendar = new Calendar("2018/2019", 35);
$calendar->year = 2018; 
$calendar->type = "poster";
$calendar->size = new Size(95, 75); 
$calendar->info(); 
?> 

</body> 
</html>
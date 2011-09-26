<?php
namespace PodcastParser;

class Podcast {
    private $feed_url;
    private $items;
    private $channel;

    public function __construct() 
    {
        $this->items = array();
        $this->channel = array();                                    
    }

    /**
     * Parse the feed
     * 
     * @param $feed_url
     * 
     * @return void
     */
    public function parseFeed($feed_url)
    {
        $this->feed_url = $feed_url;
        $file = file_get_contents($this->feed_url);
        $xml_parser = simplexml_load_string($file, null, LIBXML_NOCDATA);

        $this->channel = array(							
            'title'         =>(string) $xml_parser->channel->title,
            'link'          =>(string) $xml_parser->channel->link,
            'copyright'     =>(string) $xml_parser->channel->copyright,
            'author'        =>(string) $xml_parser->channel->author,
            'category'      =>(string) $xml_parser->channel->category,
            'image'         => new PodcastImage($xml_parser->channel->image),
            'explicit'      =>(string) $xml_parser->channel->explicit,                                        
            'description'   =>(string) $xml_parser->channel->description,
            'pubDate'       =>(string) $xml_parser->channel->pubDate,			
            'language'      =>(string) $xml_parser->channel->language,
            'lastBuildDate' =>(string) $xml_parser->channel->lastBuildDate,
            'webMaster'     =>(string) $xml_parser->channel->webMaster,
        );

        foreach ($xml_parser->channel->children()->item as $item) 
        {					
            $this->items[]  = new PodcastItem($item);
        }

        unset($xml_parser);
    }      

    /**
     * Get all Channel Info
     * 
     * @return array
     */
    function getChannelInfo() {
        return $this->channel;
    }

    /**
     * Get the podcast <b>Title</b> value
     * 
     * @return string
     */
    public function getTitle() {
        return $this->channel['title'];
    }

    /**
     * Get the podcast <b>Link</b> value
     * 
     * @return string
     */
    public function getLink() {
        return $this->channel['link'];
    }

    /**
     * Get the podcast <b>Copyright</b> value
     * 
     * @return string
     */
    public function getCopyright() {
        return $this->channel['copyright'];
    }

    /**
     * Get the podcast <b>Author</b> value
     * 
     * @return string
     */
    public function getAuthor() {
        return $this->channel['author'];
    }

    /**
     * Get the podcast <b>Category</b> value
     * 
     * @return string
     */
    public function getCategory() {
        return $this->channel['category'];
    }

    /**
     * Get the <b>PodcastImage</b> object
     * 
     * @return object PodcastImage
     */    
    public function getImage() {
        return $this->channel['image'];
    }
    
    /**
     * Get the podcast <b>Explicit</b> value
     * 
     * @return string
     */
    public function getExplicit() {
        return $this->channel['explicit'];
    }

    /**
     * Get the podcast <b>Description</b> value
     * 
     * @return string
     */    
    public function getDescription() {
        return $this->channel['description'];
    }

    /**
     * Get the podcast <b>PubDate</b> value
     * @example : Wed, 15 Jun 2005 19:00:00 GMT (RFC 2822)
     * 
     * @return string
     */        
    public function getPubDate() {
        return $this->channel['pubDate'];
    }

    /**
     * Get the Podcast <b>PubDate</b> value convert to DateTime
     * 
     * @return object DateTime
     */    
    public function getPubDateTime() {
        return $this->getDateTime($this->channel['pubDate']);
    }
    
    /**
     * Get the podcast <b>Language</b> value
     * 
     * @return string
     */    
    public function getLanguage() {
        return $this->channel['language'];
    }

    /**
     * Get the podcast <b>LastBuildDate</b> value
     * @example : Wed, 15 Jun 2005 19:00:00 GMT (RFC 2822)
     * 
     * @return string
     */    
    public function getLastBuildDate() {
        return $this->channel['lastBuildDate'];
    }

    /**
     * Get the Podcast <b>LastBuildDate</b> value convert to DateTime
     * 
     * @return object DateTime
     */
    public function getLastBuildDateTime() {
        return $this->getDateTime($this->channel['lastBuildDate']);
    }    
    
    /**
     * Get the podcast <b>WebMaster</b> value
     * 
     * @return string
     */    
    public function getWebMaster() {
        return $this->channel['webMaster'];
    }
    
    /**
     * Get an array of PodcastItem
     * 
     * @return array of PodcastItem objects
     */
    public function getPodcastItems() {
        return $this->items;
    }    
    
    private function getDateTime() {
        return \DateTime::createFromFormat(\DateTime::RFC2822, $date_value);
    }    	
}

class PodcastImage {
    public $image;
    
    public function __construct($simple_xml_object) {
        $this->image = array(
            'url'   => $simple_xml_object->url,
            'title' => $simple_xml_object->title,
            'link'  => $simple_xml_object->link,
        );
    }
    
    /**
     * Get all Podcast Image Info
     * 
     * @return array
     */        
    public function getImageInfo() {
        return $this->image;
    }
    
    /**
     * Get the Podcast Image <b>url</b> value
     * 
     * @return string
     */     
    public function getUrl() {
        return $this->image['url'];
    }
    
    /**
     * Get the Podcast Image <b>Title</b> value
     * 
     * @return string
     */     
    public function getTitle() {
        return $this->image['title'];
    }
    
    /**
     * Get the Podcast Image <b>Link</b> value
     * 
     * @return string
     */     
    public function getLink() {
        return $this->image['link'];
    }
}

class PodcastItem {
    public $metadata;
    public $enclosure;
    /**
    Converts from SimpleXMLElement to a normal user object
    This means you can serialise/cache it if you wish
    */
    public function __construct($xml_obj)
    {
        $this->metadata = array(
            'title'         => (string) $xml_obj->title,
            'link'          => (string) $xml_obj->link,
            'guid'          => (string) $xml_obj->guid,
            'description'   => (string) $xml_obj->description,
            'category'      => (string) $xml_obj->description,			
            'pubDate'       => (string) $xml_obj->pubDate,
            'author'        => (string) $xml_obj->author,
        );
        $enc_tmp =  $xml_obj->enclosure->attributes();
        $this->enclosure = array(
            'url'=> 		(string) $enc_tmp->url,
            'length'=> 		(string) $enc_tmp->length,
            'type'=> 		(string) $enc_tmp->type,
        );
    }
    
    /**
     * Get the Podcast Item <b>Title</b> value
     * 
     * @return string
     */     
    public function getTitle() {
        return $this->metadata['title'];
    }
    
    /**
     * Get the Podcast Item <b>Link</b> value
     * 
     * @return string
     */
    public function getLink() {
        return $this->metadata['link'];
    }
    
    /**
     * Get the Podcast Item <b>Guid</b> value
     * 
     * @return string
     */
    public function getGuid() {
        return $this->metadata['guid'];
    }
    
    /**
     * Get the Podcast Item <b>Description</b> value
     * 
     * @return string
     */
    public function getDescription() {
        return $this->metadata['description'];
    }
    
    /**
     * Get the Podcast Item <b>Category</b> value
     * 
     * @return string
     */
    public function getCategory() {
        return $this->metadata['category'];
    }
    
    /**
     * Get the Podcast Item <b>PubDate</b> value
     * @example : Wed, 15 Jun 2005 19:00:00 GMT (RFC 2822)
     * 
     * @return string
     */
    public function getPubDate() {
        return $this->metadata['pubDate'];
    }
    
    /**
     * Get the Podcast Item <b>PubDate</b> value convert to DateTime
     * 
     * @return object DateTime
     */
    public function getPubDatetime() {
        return \DateTime::createFromFormat(\DateTime::RFC2822, $this->metadata['pubDate']);
    }
    
    /**
     * Get the Podcast Item <b>Author</b> value
     * 
     * @return string
     */
    public function getAuthor() {
        return $this->metadata['author'];
    }
}
?>
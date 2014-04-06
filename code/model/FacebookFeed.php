<?php
/**
 * STOLEN FROM: http://www.acornartwork.com/blog/2010/04/19/tutorial-facebook-rss-feed-parser-in-pure-php/
 * EXAMPLE:
 *		//Run the function with the url and a number as arguments
 *		$fb = new TheFaceBook_communicator();
 *		$dos = $fb->fetchFBFeed('http://facebook.com/feeds/status.php?id=xxxxxx&viewer=xxxxxx&key=xxxxx&format=rss20', 3);
 *		//Print Facebook status updates
 *		echo '<ul class="fb-updates">';
 *			 foreach ($dos as $do) {
 *					echo '<li>';
 *					echo '<span class="update">' .$do->Description. '</span>';
 *					echo '<span class="date">' .$do->Date. '</span>';
 *					echo '<span class="link"><a href="' .$do->Link. '">more</a></span>';
 *					echo '</li>';
 *			 }
 *		echo '</ul>';
 *
 *
 *
 *
 **/


class TheFaceBook_communicator extends RestfulServer {

	/**
  	 *@returns DataObjectSet
  	 **/

	function fetchFBFeed($url, $maxnumber = 1, $timeFormat = 'F jS Y, H:i') {
	/* The following line is absolutely necessary to read Facebook feeds. Facebook will not recognize PHP as a browser and therefore won't fetch anything. So we define a browser here */
		ini_set('user_agent', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3');
		$updates = simplexml_load_file($url);  //Load feed with simplexml
		$arrayList = new ArrayList();  //Initialize empty array to store statuses
		foreach ( $updates->channel->item as $fbUpdate ) {
			if ($maxnumber == 0) {
				break;
			}
			else {
				$desc = $fbUpdate->description;
				//Add www.facebook.com to hyperlinks
				$desc = str_replace('href="', 'href="http://www.facebook.com', $desc);
				//Converts UTF-8 into ISO-8859-1 to solve special symbols issues
				$desc = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $desc);
				//Get status update time
				$pubdate = strtotime($fbUpdate->pubDate);
				$propertime = gmdate($timeFormat, $pubdate);  //Customize this to your liking
				//Get link to update
				$linkback = $fbUpdate->link;
				//Store values in array
				$fbItem = array(
					'Description' => $desc,
					'Date' => $propertime,
					'Link' => $linkback
				);
				$arrayList->push(new ArrayData($fbItem));
				$maxnumber--;
			}
		}
		return $arrayList;
	}
}


class TheFaceBook_IFrame extends ViewableData {
	/**
	 *@link  http://developers.facebook.com/docs/reference/plugins/activity/
	 *@see: http://developers.facebook.com/docs/reference/plugins/activity/
	 *
	 **/

	private static $get_variables = array(
		"site" => "www.mysite.com",
		"width" => "300",
		"height" => "300",
		"header" => "true",
		"colorscheme" => "light",
		"font" => "arial",
		"border_color" => "red",
		"recommendations" => "true"
	);

	private static $facebook_url = 'http://www.facebook.com/plugins/activity.php';

	private static $iframe_settings = array(
		 "scrolling" => "no",
		 "frameborder" => "0",
		 "style" => "border:none; overflow:hidden; width:300px; height:300px;",
		 "allowTransparency" => "true"
	);

	function TheFaceBookFrame() {
		$url = self::$facebook_url.'?'.implode("&amp;",self::$variables());
		self::$iframe_settings["src"] = $url;
		$str = '';
		foreach(self::$iframe_settings as $key => $value) {
			$str .= "$key=\"$value\" ";
		}
		return '<iframe '.$str.'></iframe>';
	}

}
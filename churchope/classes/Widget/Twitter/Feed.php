<?php

class Widget_Twitter_Feed
{

	public function getFeedItems($username, $feedItemsNum = 5, $encode = true, $extractLinks = true, $extractUsers = true)
	{
		$aMessage = $this->getTwitterMessages($username, $feedItemsNum);

		if (empty($aMessage))
		{
			return array();
		}
		if (!is_wp_error($aMessage))
		{
			$aMessage = $this->parseMessages($aMessage, $encode, $extractLinks, $extractUsers);
			return $aMessage;
		}
		else
		{
			return "Error";
		}
	}

	protected function getTwitterMessages($username, $num = 5)
	{
		include_once(ABSPATH . WPINC . '/feed.php');
		$aMessage = fetch_feed('http://twitter.com/statuses/user_timeline/' . $username . '.rss');


		if (!is_wp_error($aMessage))
		{ // Checks that the object is created correctly 
			// Figure out how many total items there are, but limit it to 5. 
			$maxitems = $aMessage->get_item_quantity($num);

			// Build an array of all the items, starting with element 0 (first element).
			$aMessage = $aMessage->get_items(0, $maxitems);
		}

		return $aMessage;
	}

	protected function parseMessages($aMessage, $encode, $extractLinks, $extractUsers)
	{
		$aParsedMsg = array();



		foreach ($aMessage as $tweet)
		{
			$content = " " . substr(strstr($tweet->get_description(), ': '), 2, strlen($tweet->get_description())) . " ";

			if ($encode)
			{
				$content = utf8_encode($content);
			}

			if ($extractLinks)
			{
				$content = $this->extractHyperlinks($content);
			}

			if ($extractUsers)
			{
				$content = $this->extractUsers($content);
			}
			$item['link'] = $tweet->get_link();
			$item['description'] = $content;
			$pubdate = $tweet->get_item_tags('', 'pubDate');
			$item['date-posted'] = $this->getMessageTimestamp($pubdate[0]['data']);
			$aParsedMsg[] = $item;
		}
		return $aParsedMsg;
	}

	protected function getMessageTimestamp($publishDate)
	{

		$h_time = null;
		$time = strtotime($publishDate);
		if (( abs(time() - $time) ) < 864000)
		{
			$h_time = sprintf(__('%s ago', 'churchope'), human_time_diff($time));
		}
		else
		{
			$h_time = date(__('Y/m/d', 'churchope'), $time);
		}
		return sprintf(__('%s', 'churchope'), ' <span class="twitter-timestamp">' . $h_time . '</span>');
	}

	private function extractHyperlinks($text)
	{

		$text = preg_replace('/\b([a-zA-Z]+:\/\/[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i', "<a href=\"$1\" class=\"twitter-link\">$1</a>", $text);

		$text = preg_replace('/\b(?<!:\/\/)(www\.[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i', "<a href=\"http://$1\" class=\"twitter-link\">$1</a>", $text);

		$text = preg_replace("/\b([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})\b/i", "<a href=\"mailto://$1\" class=\"twitter-link\">$1</a>", $text);

		$text = preg_replace('/([\.|\,|\:|\�|\�|\>|\{|\(]?)#{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/#search?q=$2\" class=\"twitter-link\">#$2</a>$3 ", $text);
		return $text;
	}

	private function extractUsers($text)
	{
		$text = preg_replace('/([\.|\,|\:|\�|\�|\>|\{|\(]?)@{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/$2\" class=\"twitter-user\">@$2</a>$3 ", $text);
		return $text;
	}
}
?>
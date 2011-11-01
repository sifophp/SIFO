<?php
/**
 * LICENSE
 *
 * Copyright 2010 Jorge Tarrero
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Sifo;

include ROOT_PATH . '/libs/EpiClasses/EpiCurl.php';

/**
 * Retreive data of a youtube video from API.
 *
 * @requires EpiCurl.php, SimpleXML
 * @author Jorge Tarrero (thedae@gmail.com)
 * @version 0.1
 *
 * Example of usage in SEOframework:
 * 		$api = $this->getClass( 'APIYoutube' );
 * 		$video_url = 'http://www.youtube.com/watch?v=ybNJb6EuU1Y'; OR $video_url = 'ybNJb6EuU1Y';
 * 		$video_data = $api->getVideoData( $video_url );
 *
 * The format of the returned data is an array with these keys:
 * 		[video_id] => ybNJb6EuU1Y
 * 		[url_player] => http://www.youtube.com/watch?v=ybNJb6EuU1Y&feature=youtube_gdata
 * 		[title] => Video title
 * 		[description] => Video description
 * 		[keywords] => Array
 * 		(
 * 			[0] => keyword1
 * 			[1] =>  keyword2
 * 			...
 * 			[N] => keywordN
 *		)
 *		[url_embed] => http://www.youtube.com/v/ybNJb6EuU1Y?f=videos&app=youtube_gdata
 *		[duration] => Duration (in seconds)
 *		[thumbnails] => Array
 *		(
 *			[0] => http://i.ytimg.com/vi/ybNJb6EuU1Y/2.jpg
 *			[1] => http://i.ytimg.com/vi/ybNJb6EuU1Y/1.jpg
 *			[2] => http://i.ytimg.com/vi/ybNJb6EuU1Y/3.jpg
 *		)
 */
class APIYoutube
{
	/**
	 * Youtube API url to retreive data.
	 *
	 * @var string
	 */
	protected $api_url = 'http://gdata.youtube.com/feeds/api/videos/';

	/**
	 * Media namespace to format the XML.
	 *
	 * @var string
	 */
	protected $media_namespace = 'http://search.yahoo.com/mrss/';

	/**
	 * Get video data from Youtube API.
	 *
	 * @param string $video_url Video URL or video ID.
	 * @return array
	 */
	public function getVideoData( $video_url )
	{
		$video_id = $this->_getVideoId( $video_url );

		$multi_curl = EpiCurl::getInstance();
		$curl = curl_init( $this->api_url . $video_id );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		$response = $multi_curl->addCurl( $curl );

		if ( 200 !== $response->code )
		{
			return false;
		}

		try
		{
			$xml_data = @new SimpleXMLElement( $response->data );
		}
		catch( Exception $e )
		{
			if ( 'Invalid id' === $response->data )
			{
				return false;
			}
		}

		$video_data['video_id'] = $video_id;
		$video_data['url_player'] = ( string ) $xml_data->link[0]->attributes()->href[0];

		$media_group = $xml_data->children( $this->media_namespace );
		$video_data['title'] = ( string ) $media_group->group->title;
		$video_data['description'] = ( string ) $media_group->group->description;
		$video_data['keywords'] = explode( ',', ( string ) $media_group->group->keywords );

		if ( !isset( $media_group->group->content[0] ) )
		{
			return false;
		}

		$content_attrs = $media_group->group->content[0]->attributes();
		$video_data['url_embed'] = ( string ) $content_attrs['url'];
		$video_data['duration'] = ( int ) $content_attrs['duration'];

		foreach ( $media_group->group->thumbnail as $key => $val )
		{
			$thumb_attrs = $val->attributes();
			$thumb_width = ( int ) $thumb_attrs['width'];
			if ( $thumb_width == 120 )
			{
				$video_data['thumbnails'][] = ( string ) $thumb_attrs['url'];
			}
		}

		return $video_data;
	}

	/**
	 * Get video ID from URL.
	 *
	 * @param string $video_url Video URL or video ID.
	 * @return string
	 */
	private function _getVideoId( $video_url )
	{
		$video_id = '';
		$url_parts = parse_url( $video_url );
		if ( !isset( $url_parts['query'] ) )
		{
			$video_id = $video_url;
		}
		else
		{
			parse_str( $url_parts['query'], $query_parts );
			if ( isset( $query_parts['v'] ) )
			{
				$video_id = $query_parts['v'];
			}
		}

		return $video_id;
	}
}

?>

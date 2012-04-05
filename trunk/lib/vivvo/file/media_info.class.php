<?php

class media_info {


	function get_info($file){

		require_once(VIVVO_FS_ROOT . "lib/vivvo/framework/PEAR/MP3_Id/id3v2.php");

		$mp3 = new id3v2();

		$mp3->GetInfo($file);
		//$mp3->ShowInfo();

		$data = array();
		if( !empty( $mp3->id3v1Info['title'] ) )
			$data[] = 'Title: ' . $mp3->id3v1Info['title'];

		if( !empty( $mp3->id3v1Info['artist'] ) )
			$data[] = 'Artist: ' . $mp3->id3v1Info['artist'];

		if( !empty( $mp3->id3v1Info['album'] ) )
			$data[] = 'Album: ' . $mp3->id3v1Info['album'];

		if( !empty( $mp3->id3v1Info['year'] ) )
			$data[] = 'Year: ' . $mp3->id3v1Info['year'];

		if( !empty( $mp3->id3v1Info['track'] ) )
			$data[] = 'Track: #' . $mp3->id3v1Info['track'];

		if( !empty( $mp3->mpegInfo['PlayTime'] ) )
			$data[] = 'Play time: ' . $mp3->mpegInfo['PlayTime'];

		if( !empty( $mp3->mpegInfo['Bitrate'] ) )
			$data[] = 'Bitrate: ' . $mp3->mpegInfo['Bitrate'] . ' kbps';

		if( !empty( $mp3->mpegInfo['SamplingRate'] ) )
			$data[] = 'Sampling rate: ' . round( $mp3->mpegInfo['SamplingRate'] / 1000, 1) . ' KHz';

		if($data)
			return implode("\n", $data);

		else
			return false;

	}
}

#EOF
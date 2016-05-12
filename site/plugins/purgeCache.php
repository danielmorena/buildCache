<?php

kirby()->routes(array(
	array(
		"pattern" => "purgeCache",
		"method" => "POST",
		"action"  => function() {
			kirby()->cache()->flush();
			return true;
		}
	)
));
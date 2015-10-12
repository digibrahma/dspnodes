<?php
		echo printLiveCells('DSP_REQ','','','Bid Requests');
		echo printLiveCells('DSP_RESP','','','<nobr>Bid Response</nobr>');
		echo printLiveCells('DSP_RESP','div','DSP_REQ','% Response','%','');
		echo printLiveCells('DSP_WIN','','','Bids Won');
		echo printLiveCells('DSP_WIN','div','DSP_RESP','% Won','%','');
		
		echo MergeRow('Spendings (USD $)');
		echo printLiveCells('DSP_BIDPRICE','','','Bid Price');
		echo printLiveCells('DSP_WINPRICE','','','Win Price');
		echo printLiveCells('DSP_WINPRICE','divk','DSP_BIDPRICE','% Won','%','');
		
		
		
		
		
?>
<?php

    function toFixed($number) {
        return number_format($number, 3, ".", "");
    }
    
    require __DIR__.'/config.php';
    
	require __DIR__.'/src/edsonmedina/bittrex/Client.php';

	use edsonmedina\bittrex\Client;

	$b = new Client ($key, $secret);
    
    $btcMkt = $b->getMarketSummary('USDT-BTC');
    $btcLast = $btcMkt[0]['Last'];
    $btcBid  = $btcMkt[0]['Bid'];
    $btcAsk  = $btcMkt[0]['Ask'];
    
    $btcMean = ( $btcLast + $btcBid + $btcAsk ) / 3;
	$balances = $b->getBalances();
    
    $saldoTotalMBTC = 0;
    $saldoTotalUSD  = 0;
    
    foreach($balances as $balance)
    {
        $saldo = $balance['Balance'];
        if($saldo > 0)
        {
            $currency = $balance['Currency'];
            try{
                
                if($currency != 'BTC') 
                {
                    $mkt = $b->getMarketSummary('BTC-'.$currency);

                    $last        = $mkt[0]['Last'];
                    $bid         = $mkt[0]['Bid'];
                    $ask         = $mkt[0]['Ask'];
                    $mean        = ( $last + $bid + $ask ) / 3;
                    $mBtcMean    = $mean * 1000;
                    $dollarValue = $mean * $btcMean;
                }
                else
                {
                    $mean        = 1;
                    $mBtcMean    = 1000;
                    $dollarValue = $btcMean;
                }
                
                $mBtcMean    = toFixed($mBtcMean, 3);
                $dollarValue = toFixed($dollarValue, 3);
                $saldoMBTC   = toFixed($mBtcMean * $saldo, 3);
                $saldoUSD    = toFixed($dollarValue * $saldo, 3);
                $saldo       = toFixed($saldo, 3);
                echo "Moneda: $currency,\t Valor mBTC: $mBtcMean,\t Valor USD: $dollarValue,\t Saldo: $saldo,\t Saldo mBTC: $saldoMBTC,\t Saldo USD: $saldoUSD\r\n";
                
                $saldoTotalMBTC += $saldoMBTC;
                $saldoTotalUSD  += $saldoUSD;
            }
            catch(\Exception $e){
                echo "Error obteniendo mercado de: $currency\r\n";
            }
        }
    }
    
    echo "SALDO TOTAL USD: $saldoTotalUSD, SALDO TOTAL MBTC: $saldoTotalMBTC";
    echo "\n\n";
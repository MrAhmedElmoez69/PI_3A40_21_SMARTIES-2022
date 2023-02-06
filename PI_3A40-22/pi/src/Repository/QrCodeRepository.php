<?php

namespace App\Repository;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\QrCode;
use http\Env\Response;
use SGK\BarcodeBundle\Generator\Generator;

class QrCodeRepository{

    /**
     * @var BuilderInterface
     */
    protected $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function qrcode($query)
    {
        $url = 'http://localhost/PI_3A40_21_SMARTIES/pi/public/index.php/produit/explore_produit/';

        $objDateTime = new \DateTime('NOW');
        $dateString = $objDateTime->format('d-m-Y H:i:s');

        $path = dirname(__DIR__, 2).'/public/img/';

        // set qrcode
        $result = $this->builder
            ->data($url.$query)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(400)
            ->margin(10)
            ->labelText($dateString)
            ->labelAlignment(new LabelAlignmentCenter())
            ->labelMargin(new Margin(15, 5, 5, 5))
            ->logoPath($path.'logo1.png')
            ->logoResizeToWidth('200')
            ->logoResizeToHeight('100')
            ->backgroundColor(new Color(255, 255, 255))
            ->build()
        ;

        //generate name
        $namePng = uniqid('', '') . '.png';

        //Save img png
        $result->saveToFile($path.'/'.$namePng);

        return $result->getDataUri();
    }

    public function barcode(Response $response){

        $options = array(
            'code'   => 'http://localhost/PI_3A40_21_SMARTIES/pi/public/index.php/commande/achatfront/',
            'type'   => 'c93',
            'format' => 'html',
            'width'  => 10,
            'height' => 100,
            'color'  => array(127, 127, 127),
        );


        $barcode = $this->get('sgk_barcode.generator')->generate($options);

        $savePath = '/img/';
        $fileName = 'barcode.html';

        file_put_contents($savePath.$fileName, $barcode);
        //file_put_contents($savePath.$fileName, base64_decode($barcode));
        return new Response('<img src="data:image/png;base64,'.$barcode.'" />');
    }
}

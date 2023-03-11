<?php
namespace Liloo\Qrcode;

use \Exception;
use Qrcode\Input;
use Qrcode\Mask;
use Qrcode\Spec;
use Qrcode\Split;
use Qrcode\Tools;
use Qrcode\RawCode;
use Qrcode\FrameFiller;

class Render
{

    public $version;
    public $width;
    public $data;

    public function encodeMask(Input $input, $mask)
    {
        if ($input->getVersion() < 0 || $input->getVersion() > QRSPEC_VERSION_MAX) {
            throw new Exception('wrong version');
        }
        if ($input->getErrorCorrectionLevel() > QR_ECLEVEL_H) {
            throw new Exception('wrong level');
        }

        $raw = new RawCode($input);

        Tools::markTime('after_raw');

        $version = $raw->version;
        $width = Spec::getWidth($version);
        $frame = Spec::newFrame($version);

        $filler = new FrameFiller($width, $frame);
        if (is_null($filler)) {
            return null;
        }

        // inteleaved data and ecc codes
        for ($i = 0; $i < $raw->dataLength + $raw->eccLength; $i++) {
            $code = $raw->getCode();
            $bit = 0x80;
            for ($j = 0; $j < 8; $j++) {
                $addr = $filler->next();
                $filler->setFrameAt($addr, 0x02 | (($bit & $code) != 0));
                $bit = $bit >> 1;
            }
        }

        Tools::markTime('after_filler');

        unset($raw);

        // remainder bits
        $j = Spec::getRemainder($version);
        for ($i = 0; $i < $j; $i++) {
            $addr = $filler->next();
            $filler->setFrameAt($addr, 0x02);
        }

        $frame = $filler->frame;
        unset($filler);

        // masking
        $maskObj = new Mask();
        if ($mask < 0) {

            if (QR_FIND_BEST_MASK) {
                $masked = $maskObj->mask($width, $frame, $input->getErrorCorrectionLevel());
            } else {
                $masked = $maskObj->makeMask($width, $frame, (intval(QR_DEFAULT_MASK) % 8), $input->getErrorCorrectionLevel());
            }
        } else {
            $masked = $maskObj->makeMask($width, $frame, $mask, $input->getErrorCorrectionLevel());
        }

        if ($masked == null) {
            return null;
        }

        Tools::markTime('after_mask');

        $this->version = $version;
        $this->width = $width;
        $this->data = $masked;

        return $this;
    }

    public function encodeInput(Input $input)
    {
        return $this->encodeMask($input, -1);
    }

    public function encodeString8bit($string, $version, $level)
    {
        if ($string == null) {
            throw new Exception('empty string!');
            return null;
        }

        $input = new Input($version, $level);
        if ($input == null) {
            return null;
        }

        $ret = $input->append($input, QR_MODE_8, strlen($string), str_split($string));
        if ($ret < 0) {
            unset($input);
            return null;
        }
        return $this->encodeInput($input);
    }

    public function encodeString($string, $version, $level, $hint, $casesensitive)
    {

        if ($hint != QR_MODE_8 && $hint != QR_MODE_KANJI) {
            throw new Exception('bad hint');
            return null;
        }

        $input = new Input($version, $level);
        if ($input == null) {
            return null;
        }

        $ret = Split::splitStringToQRinput($string, $input, $hint, $casesensitive);
        if ($ret < 0) {
            return null;
        }

        return $this->encodeInput($input);
    }

    public static function png($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint = false)
    {
        $enc = Encode::factory($level, $size, $margin);
        return $enc->encodePNG($text, $outfile, $saveandprint = false);
    }

    public static function text($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4)
    {
        $enc = Encode::factory($level, $size, $margin);
        return $enc->encode($text, $outfile);
    }

    public static function raw($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4)
    {
        $enc = Encode::factory($level, $size, $margin);
        return $enc->encodeRAW($text, $outfile);
    }
}
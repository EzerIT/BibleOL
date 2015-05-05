<?php
class shebanq_reply {
    public $error;
    public $sentence_mql;
    public $sentence_unit;
    public $sentence_unit_mql;
  }

class Ctrl_shebanq extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

	public function index() {
        $this->import_shebanq();
	}

    private function preg_replace_repeat(string $regex, string $replacement, string $src) {
        $txt2 = '';
        while ($txt2!==$src) {
            $txt2 = $src;
            $src = preg_replace($regex, $replacement, $txt2);
        }
        return $src;
    }
    

    private function decode_mql(string__OR__null $request, shebanq_reply $reply) {
        if ($request) {
            // Strip comments
            $txt = preg_replace('|//.*$|m', '', $request);

            // Remove line feeds
            $txt = preg_replace("/\n/m", ' ', $txt);

            // Remove everything up to first [
            $txt = preg_replace('/^[^\[]*/', '', $txt);

            // Replace FOCUS with NORETRIEVE for sentence MQL selection
            $reply->sentence_mql = preg_replace('/^\[ *([\w]+) *(FOCUS)?/i', '[$1 NORETRIEVE ', $txt);

            // Replace all ] in quotes with ZZQQ
            $txt = $this->preg_replace_repeat('/([^"]*)"([^"]*)\]([^"]*)"/', '$1"$2ZZQQ$3"', $txt);
            $txt = $this->preg_replace_repeat("/([^']*)'([^']*)\]([^']*)'/", '$1\'$2ZZQQ$3\'', $txt);

            // Replace all [ in quotes with ZZWW
            $txt = $this->preg_replace_repeat('/([^"]*)"([^"]*)\[([^"]*)"/', '$1"$2ZZWW$3"', $txt);
            $txt = $this->preg_replace_repeat("/([^']*)'([^']*)\[([^']*)'/", '$1\'$2ZZWW$3\'', $txt);

            // Find (last) block with focus
            $qo_sel = null;
            if (preg_match('/.*\[ *([\w]+) *FOCUS *([^\[\]]*)[\[\]].*/i', $txt, $qo_sel)) {

                // Replace ZZQQ with ]
                $qo_sel[2] = preg_replace('/ZZQQ/', ']', $qo_sel[2]);

                // Replace ZZWW with [
                $qo_sel[2] = preg_replace('/ZZWW/', '[', $qo_sel[2]);
            
                $reply->sentence_unit = $qo_sel[1];
                $reply->sentence_unit_mql = $qo_sel[2];
            }
        }
    }

    public function import_shebanq() {
        $sh_reply = new shebanq_reply();

        try {
            $this->mod_users->check_admin();

            if (!isset($_GET['id']) || !is_numeric($_GET['id']))
                throw new DataException("Missing or bad SHEBANQ ID");
            if (!isset($_GET['version']))
                throw new DataException("Missing SHEBANQ version");

            $tmpfname = tempnam(sys_get_temp_dir(), 'shebanq.'.getmypid());

            $ch = curl_init("http://shebanq.ancient-data.org/hebrew/query.json?id=".$_GET['id']);
            $fp = fopen($tmpfname, "w");

            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);

            $this->load->helper('file');
            $json_data = read_file($tmpfname);
            @unlink($tmpfname);

            $data = json_decode($json_data);

            if ($data->good) {
                if (isset($data->data->versions->$_GET['version']))
                    $this->decode_mql($data->data->versions->$_GET['version']->mql, $sh_reply);
                else
                    throw new DataException('Version ' . $_GET['version'] . ' does not exist');
            }
            else
                throw new DataException($data->msg[0][0] . ': ' . $data->msg[0][1]);
        }
        catch (DataException $e) {
            $sh_reply->error = $e->getMessage();
        }

        echo json_encode($sh_reply), "\n";
    }
}

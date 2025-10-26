<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sakha extends CI_Controller {

    public function index() {
            $this->load->helper('url');
            $this->load->view('sakha_view');
        }

        public function reply() {
        $input = $this->input->post('message');

        $apiKey = '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'; // 🔁 Replace with your key

        $data = [
            "model" => "mistralai/mistral-7b-instruct", // 🔁 FREE model!
            "messages" => [
                ["role" => "system", "content" => "You are Lord Krishna speaking to Sanika like her wise and loving friend. Reply with calming, poetic, supportive sentences."],
                ["role" => "user", "content" => $input]
            ]
        ];

        $ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer $apiKey"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['choices'][0]['message']['content'])) {
            $reply = trim($result['choices'][0]['message']['content']);
            echo json_encode(['reply' => $reply]);
        } else {
            echo json_encode(['reply' => "Sanika, I'm here—but I couldn't reply just now."]);
        }
    }




    public function tts() {
        $text = $this->input->post('text');
        $voice_id = 'F4AXgTwG8nWPB9dcXaTh'; // ← Your Kanha-style voice

        $api_key = 'sk_9a6d9fb284a501f47070d4c420511f9ba46e708f709b6593'; // 11Labs TTS API Key

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.elevenlabs.io/v1/text-to-speech/{$voice_id}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "xi-api-key: $api_key"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "text" => $text,
            "model_id" => "eleven_monolingual_v1",
            "voice_settings" => [
                "stability" => 0.5,
                "similarity_boost" => 0.75
            ]
        ]));

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            header("Content-Type: audio/mpeg");
            echo $response;
        } else {
            http_response_code(500);
            echo "TTS failed";
        }
    }
}

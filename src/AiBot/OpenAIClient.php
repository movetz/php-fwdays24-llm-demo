<?php
declare(strict_types=1);

namespace App\AiBot;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAIClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $openAIApiKey,
    ) {

    }

    /**
     *
     * @param array $messages
     * @param string $model
     *
     * curl https://api.openai.com/v1/chat/completions \
     *  -H "Content-Type: application/json" \
     *  -H "Authorization: Bearer $OPENAI_API_KEY" \
     *  -d '{
     *       "model": "gpt-4o-mini",
     *       "messages": [
     *           {
     *               "role": "system",
     *               "content": "You are a helpful assistant."
     *           },
     *           {
     *               "role": "user",
     *               "content": "What is a LLM?"
     *           }
     *       ]
     *  }'
     *
     *  --------
     *  {
     *      "id": "chatcmpl-9szhTeYxGIYOQywZxWDoF41I4xluN",
     *      "object": "chat.completion",
     *      "created": 1722893915,
     *      "model": "gpt-4o-mini-2024-07-18",
     *      "choices": [
     *          {
     *              "index": 0,
     *              "message": {
     *                  "role": "assistant",
     *                  "content": "A Large Language Model, refers to a type of artificial intelligence model ..."
     *              },
     *              "logprobs": null,
     *              "finish_reason": "stop"
     *          }
     *      ],
     *      "usage": {
     *          "prompt_tokens": 23,
     *          "completion_tokens": 237,
     *          "total_tokens": 260
     *      },
     *      "system_fingerprint": "fp_507c9469a1"
     * }
 *
     * @return string
     */
    public function callChatCompletion(array $messages, string $model = 'gpt-4o-mini'): string
    {
        $response = $this->httpClient->request(
            'POST',
            'https://api.openai.com/v1/chat/completions',
            [
                'headers' => ['Authorization' => 'Bearer '.$this->openAIApiKey],
                'json' => [
                    'model' => $model,
                    'messages' => $messages,
                ],
            ]
        );

        $jsonResponse = $response->toArray();

        // Use just text answer from gpt model
        $message = $jsonResponse['choices'][0]['message']['content'];

        return $message;
    }

    /**
     * @param array $input
     * @param string $model
     *
     * curl https://api.openai.com/v1/embeddings \
     *      -H "Content-Type: application/json" \
     *      -H "Authorization: Bearer $OPENAI_API_KEY" \
     *      -d '{
     *          "input": "Your text string goes here",
     *          "model": "text-embedding-3-small"
     *      }'
     *
     * --------
     * {
     *      "object": "list",
     *      "data": [
     *          {
     *              "object": "embedding",
     *              "index": 0,
     *              "embedding": [
     *                  -0.006929283495992422,
     *                  -0.005336422007530928,
     *                  ...
     *                  -4.547132266452536e-05,
     *                  -0.024047505110502243
     *              ],
     *          }
     *      ],
     *      "model": "text-embedding-3-small",
     *      "usage": {
     *          "total_tokens": 5
     *      }
     * }
     *
     * @return array<float>
     *
     */
    public function callEmbeddings(string $input, string $model = 'text-embedding-3-small'): array
    {
        $response = $this->httpClient->request(
            'POST',
            'https://api.openai.com/v1/embeddings',
            [
                'headers' => ['Authorization' => 'Bearer '.$this->openAIApiKey],
                'json' => [
                    'model' => $model,
                    'input' => $input,
                ],
            ]
        );

        $jsonResponse = $response->toArray();
        $embedding = $jsonResponse['data'][0]['embedding'];

        return $embedding;
    }
}

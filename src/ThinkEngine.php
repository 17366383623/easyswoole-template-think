<?php


namespace EasySwoole\Template\Think;

use Throwable;

class ThinkEngine
{
    private $engine;

    /**
     * Think constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->engine = new \think\Template($config);
    }

    /**
     * 模板渲染
     * @param string $template
     * @param array $data
     * @return string|null
     */
    public function render(string $template, array $data = []): ?string
    {
        ob_start();
        $this->engine->fetch($template, $data);
        return ob_get_clean();
    }

    /**
     * 每次渲染完成都会执行清理
     * @param string|null $result
     * @param string $template
     * @param array $data
     * @param array $options
     */
    public function afterRender(?string $result, string $template, array $data = [], array $options = [])
    {

    }

    /**
     * 异常处理
     * @param Throwable $throwable
     * @return string
     * @throws Throwable
     */
    public function onException(\Throwable $throwable): string
    {
        throw $throwable;
    }
}
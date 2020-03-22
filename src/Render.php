<?php

namespace EasySwoole\Template\Think;

use EasySwoole\Component\Process\Exception;
use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Template\Think\Socket\Protocol;
use EasySwoole\Template\Think\Socket\UnixClient;
use EasySwoole\Template\Think\Process\RenderProcess;
use EasySwoole\Template\Think\Process\RenderProcessConfig;

class Render
{
    use Singleton;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array $thinkConfig
     */
    private $thinkConfig;

    /**
     * @var render $worker
     */
    private $worker;

    /**
     * init render dev
     * @param Config $config
     * @param \swoole_server $server
     * @param array $options
     * @return Render
     * @throws Component\Process\Exception
     * @throws RenderException
     * @throws Exception
     */
    public function init(Config $config, array $options = [], \swoole_server $server = NULL): Render
    {
        $this->setConfig($config, $options);
        $this->setThinkConfig($options);
        $server = $server?:ServerManager::getInstance();
        $this->attachRenderServer($server);
        return $this;
    }


    /**
     * set Think Engine Config
     *
     * @param array $options
     * @throws RenderException
     */
    public function setThinkConfig(array $options): void
    {
        $config = $this->config;
        if(!$config->getViewPath() || !$config->getCachePath()) {
            throw new RenderException('default view path or cache path is missing');
        }
        $options['view_path'] = $config->getViewPath();
        $options['cache_path'] = $config->getCachePath();
        $options['view_suffix'] = array_key_exists('view_suffix',$options)?$options['view_suffix']:'html';
        $this->thinkConfig = $options;
    }

    /**
     * set render engine config
     *
     * @param Config $config
     */
    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }


    /**
     * render task
     *
     * @param string $path
     * @param array $data
     * @param array $options
     * @return mixed
     */
    public function view(string $path, array $data = [], array $options = []): ?string
    {
        $sockFileFd = $this->getRenderProcess();
        $client = new UnixClient($sockFileFd);
        $client->send(Protocol::pack(serialize(([
            'template' => $path,
            'data' => $data,
            'options' => array_merge($this->thinkConfig, $options)
        ]))));
        $recvData = $client->recv($this->config->getTimeout());
        if($recvData){
            return unserialize(Protocol::unpack($recvData));
        }
        return NULL;
    }

    /**
     * create render process
     *
     * @param \swoole_server $server
     * @throws Component\Process\Exception
     * @throws Exception
     */
    public function attachRenderServer(\swoole_server $server): void
    {
        $processList = $this->generateProcessList();
        foreach ($processList as $pro){
            $server->addProcess($pro->getProcess());
        }
    }

    /**
     * create render process
     *
     * @return array
     * @throws Component\Process\Exception
     * @throws Exception
     */
    protected function generateProcessList():array
    {
        $array = [];
        for ($i = 1;$i <= $this->config->getWorkerNum();$i++){
            $config = new RenderProcessConfig();
            $config->setProcessName("Render.{$this->config->getSocketPrefix()}Worker.{$i}");
            $config->setSocketFile(__DIR__."/Render.{$this->config->getSocketPrefix()}Worker.{$i}.sock");
            $config->setAsyncCallback(false);
            $array[$i] = new RenderProcess($config);
            $this->worker[$i] = $array[$i];
        }
        return $array;
    }

    /**
     * get unix socket resource fd
     *
     * @return string
     */
    private function getRenderProcess(): string
    {
        mt_srand();
        $id = rand(1,$this->config->getWorkerNum());
        return __DIR__."/Render.{$this->config->getSocketPrefix()}Worker.{$id}.sock";
    }
}
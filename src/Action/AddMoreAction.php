<?php

/*
 * This file is part of the Sacrpkg CrudBundle package.
 *
 * (c) Oleg Bruyako <jonsm2184@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace sacrpkg\CrudBundle\Action;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Add action for more items.
 */
class AddMoreAction extends ActionAbstract
{
    /**
     * Function for process with each item.
     *
     * @var function
     */ 
    protected $rowprocess;
    
    /**
     * {@inheritdoc}
     */
    public function execute($params = []): Response
    {
        $isRedirect = true;
        $dublicate_names = null;
        
        $usetr = $params['use_translate'] ?? false;
		$usetrfile = $params['use_translatefile'] ?? false;
        
		$row = new $this->entity();

        $form = $this->getForm($this->formclass, $row);
 
        if ($this->request->getMethod() == 'POST') {
            $form->handleRequest($this->request);
            if ($this->request->get('save')) {
                $this->beforePersistExecute($this->em, $row);  
                if ($form->isValid()) {

                    $this->em->getConnection()->beginTransaction();
                    try {
                                              
                        $names = explode("\n", trim($row->getMorename()));
                        $names = str_replace('\r', '', $names);
                        $keys = array_map(function($item){return preg_replace('/[^a-z0-9\-]/', '', trim(strtolower($item)));}, $names);
                        $keys = array_unique($keys);
                        $names_unique = array_intersect_key($names, $keys);
                        foreach($names_unique as $name) {
                            
                            $name = trim($name);
                            $name = str_replace('  ', ' ', $name);
                            $name1 = str_replace('\r', '', $name);
                            if (preg_replace('/[^a-z0-9\-]/', '', trim(strtolower($name1)))) {
                                $dublitem = $this->em->getRepository($this->entity)
                                        ->findOneBy($row->getDublicateParam($name));
                                $dublitem1 = $this->em->getRepository($this->entity)
                                        ->findOneBy($row->getDublicateParam($name1));
                                if ($dublitem || $dublitem1) {
                                    $dublicate_names[] = $name1;
                                } else {
                                    $item = clone($row);
                                    $func = $this->rowprocess;
                                    if (is_callable($func)) {
                                        $func($this->em, $item, $this->request, $name1);
                                    } else
                                        $this->defaultRowProcess($this->em, $item, $this->request, $name1);
                                    $this->em->persist($item);
                                    $this->beforeSaveExecute($this->em, $item);
                                }
                            }
                        }

                        $this->em->flush();
                 
                        $this->em->getConnection()->commit();
                        if ($dublicate_names)
                            throw new ExceptionActionMore(implode("\n", $dublicate_names));
                        $this->flashMessage('notice', 'Записи добавлены.');
                        $isSuccess = true;
                        $this->afterSaveExecute($this->em, $row);
                        return $this->redirectToRoute($this->homeroute);
                    } catch (ExceptionActionMore $e) {
                        $isRedirect = false;
                        $isSuccess = false;
                        $row->setMorename($e->getMessage());
                        $form = $this->container->get('form.factory')->create($this->formclass, $row, []);
                        $this->flashMessage('error', "Не все элементы добавлены");
                    } catch (\Exception $e) {
                        $this->em->getConnection()->rollback();
                        $isSuccess = false;
                        $this->flashMessage('error', $e->getMessage());
                    }
                }
            }
        } 

        return $this->renderForm($form, [
            'params' => $this->params,
            'item' => $row,           
        ]);
    }
    
    /**
     * Set function for process with each item.
     *
     * @param function $rowprocess
     *
     * @return $this
     */
    public function setRowProcess($rowprocess): self
    {
        $this->rowprocess = $rowprocess;
        return $this;
    }
    
    /**
     * Set function for process with each item.
     *
     * @param EntityManager $em
     * @param object $row
     * @param Request $request
     * @param array $line
     *
     * @return void
     */
    private function defaultRowProcess(EntityManager $em, $row, Request $request, $line)
    {
        $line = str_replace("\r", '', $line);
        $uri = $this->prepareUri(str_replace(' ', '-', strtolower($line)));
        
        $row->setName($line)
            ->setUri($uri);

        $lang = $em->getRepository('App:Language')->findOneBy(['code_iso_639_1' => ['en', 'EN']]);
        
        $entity = $this->entity;
        
        $classname = $row->getTanslateClass();
        $langtempl = new $classname();        
		foreach ($entity::getTranslateField() as $field) {
            $method = 'set' . str_replace('_', '', ucwords($field, '_'));
            $name = $field . $lang->getSlug();
            $langtempl->$method($line);
            $langtempl->setLanguage($lang);

        }
        $em->persist($langtempl);
        $row->addTranslate($langtempl);        
    }
    
    /**
     * Prepare uri.
     *
     * @param string $str
     *
     * @return string
     */
    private function prepareUri(string $str): string
    {
        return preg_replace('/[^a-z0-9\-]/', '', trim(strtolower($str)));
    }
}

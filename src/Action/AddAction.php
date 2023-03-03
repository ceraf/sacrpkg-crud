<?php

namespace sacrpkg\CrudBundle\Action;

use Symfony\Component\HttpFoundation\Request;

class AddAction extends ActionAbstract
{
    public function execute($params = [])
    {
		$row = new $this->entity();
        $this->beforeInitExecute($this->em, $row);
        
        $form = $this->getForm($this->formclass, $row);
 
        if ($this->request->getMethod() == 'POST') {
            $form->handleRequest($this->request);
            if ($this->request->get('save')) {
                if ($form->isValid()) {
                    $this->em->getConnection()->beginTransaction();
                    try {
                        $this->beforePersistExecute($this->em, $row);  
                        $this->em->persist($form->getData());
                        
                        $this->saveFields($row, $this->request, $params);                                
                        $errors = $this->beforeSaveExecute($this->em, $row);
                
                        if ($errors) {
                            if (is_array($errors))
                                $errors = implode('<br>', $errors);
                            throw new ExceptionCustomString($errors);
                        }

                        $this->em->flush();

                        $this->em->getConnection()->commit();
                        $this->flashMessage('notice', 'Запись сохранена.');
                        $isSuccess = true;
                    } catch (ExceptionCustomString $e) {
                        $this->em->getConnection()->rollback();
                        $isSuccess = false;
                        $this->flashMessage('error', $e->getMessage()); 
                    } catch (\Exception $e) {
                        $this->em->getConnection()->rollback();
                        $isSuccess = false;
                        $this->flashMessage('error', $e->getMessage());
                    }
                    
                    if ($isSuccess) {
                        try {
                            $this->afterSaveExecute($this->em, $row);
                        
                            if (!($params['not_use_redirect'] ?? null)) {
                                return $this->redirectToRoute($this->homeroute, $this->homeparams);
                            } else {
                                return $this->redirectToRoute($this->editroute,
                                    ['action' => 'edit', 'id' => $row->getId()]);
                            }
                        } catch (\Exception $e) {
                            $this->flashMessage('error', $e->getMessage());
                        }
                    }
                }
            }
        } 

        return $this->renderForm($form, [
            'params' => $this->params,
            'item' => $row,           
        ]);
    }
}

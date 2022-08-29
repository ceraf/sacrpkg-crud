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

use Symfony\Component\HttpFoundation\Response;

/**
 * Edit action for more items.
 */
class EditAction extends ActionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function execute($params = []): Response
    {
        $id = $params['id'];
        $usetr = $params['use_translate'] ?? false;
		$usetrfile = $params['use_translatefile'] ?? false;
        
        if (!$id)
            return $this->notFoundObject();
        

		$row = $this->em->getRepository($this->entity)
				->find($id);
        if (!$row)
            return $this->notFoundObject();

        $form = $this->getForm($this->formclass, $row);
        $this->form_item = $form;
 
        if ($this->request->getMethod() == 'POST') {
            $form->handleRequest($this->request);
            if ($this->request->get('save')) {
                if ($form->isValid()) {
                    $this->em->getConnection()->beginTransaction();
                    try {
                        $isSuccess = true;

                        $errors = $this->beforeSaveExecute($this->em, $row);
                        if ($errors) {
                            throw new ExceptionCustomString($errors);
                        }
                        $this->saveFields($row, $this->request, $params);
                        $this->em->flush();
 
                        $this->em->getConnection()->commit();
                        $this->flashMessage('notice', 'Запись сохранена.');
                        
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
                        $this->afterSaveExecute($this->em, $row);

                        if (!($params['not_use_redirect'] ?? null))
                            return $this->redirectToRoute($this->homeroute, $this->homeparams);
                        else
                            return $this->redirectToRoute($this->editroute,
                                ['action' => 'edit', 'id' => $row->getId()]); 
                    }
                }
            } elseif ($this->request->get('markdelete'))
                return $this->markdelete($params);
        } 

        return $this->renderForm($form, [
            'params' => $this->params,
            'item' => $row,           
        ]);
    }
    
    /**
     * Mark delete item.
     *
     * @param array $params
     *
     * @return Response
     */
    protected function markdelete($params)
    {
        $id = $params['id'];
		if ($this->request->getMethod() == 'POST') {
			if ($id) {
				$row = $this->em
						->getRepository($this->entity)
						->find($id);
				if ($row && method_exists($row, 'hide')) {
					try {       

						$this->em->getConnection()->beginTransaction();
						$row->hide(true);
						$this->em->flush();
						$this->flashMessage('notice', 'Запись удалена.');
						$this->em->getConnection()->commit();

                    } catch (\Exception $e) {
						$this->flashMessage('notice', 'При удалении произошла ошибка.');
						$this->em->getConnection()->rollback();
					}
				} else {
					$this->flashMessage('error', 'При удалении произошла ошибка.');
				}
			}
        }
        return $this->redirectToRoute($this->homeroute, $this->homeparams);
    }    
    
    protected function notFoundObject(): Response
    {
        $this->flashMessage('error', 'Объект не найден.');
        return $this->redirectToRoute($this->homeroute, $this->homeparams);
    }
}

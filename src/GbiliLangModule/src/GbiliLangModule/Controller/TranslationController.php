<?php
namespace GbiliLangModule\Controller;

/**
 * scs : Symptom cause solution
 */
class TranslationController extends \Zend\Mvc\Controller\AbstractActionController
{
    /**
     *
     */
    public function indexAction()
    {
        return new \Zend\View\Model\ViewModel(array(
            'textdomains' => $this->getServiceLocator()->get('textdomain')->getTextdomains(),
            'messages' => $this->messenger()->getMessages(),
        ));
    }

    public function mergeAction()
    {
        $form = new \GbiliLangModule\Form\Merge();

        if (!$this->request->isPost()) {
            return new \Zend\View\Model\ViewModel(array(
                'form' => $form,
                'messages' => $this->messenger()->getMessages(),
            ));
        }
        $form->setData($this->request->getPost());
        if (!$form->isValid()) {
            return new \Zend\View\Model\ViewModel(array(
                'form' => $form,
                'messages' => $this->messenger()->getMessages(),
            ));
        }

        $this->getServiceLocator()->get('translationMerger')->mergeAllTextdomainTranslations();

        $this->messenger()->addMessage('Translations where merged successfully');

        return new \Zend\View\Model\ViewModel(array(
            'form' => $form,
            'messages' => $this->messenger()->getMessages(),
        ));
    }

    /**
     *
     */
    public function bulkAction()
    {
        $form               = new \GbiliLangModule\Form\BulkTranslate();
        $translationStorage = $this->getServiceLocator()->get('translationStorage');
        $textdomain         = $this->params()->fromRoute('textdomain');
        $locale             = $this->locale();
        $translations       = $translationStorage->getTranslations($textdomain, $locale);

        if (empty($translations)) {
            $this->messenger()->addMessage('There are no strings to translate', 'warning');
            return $this->redirect()->toRoute('lang_translation_index_route', array(
                'controller' => 'lang_translate_controller', 
                'action'     => 'index', 
                'textdomain' => null
            ), true);
        }

        $form->addTranslationElements($translations, $textdomain, $locale);

        if (!$this->request->isPost()) {
            return new \Zend\View\Model\ViewModel(array(
                'form' => $form
            ));
        }
        $form->setData($this->request->getPost());

        if (!$form->isValid()) {
            return new \Zend\View\Model\ViewModel(array(
                'form' => $form
            ));
        }

        $submittedTranslations = array_diff_key($form->getData(), array_flip(array('submit')));
        foreach ($submittedTranslations as $locale => $fieldsets) {
            foreach ($fieldsets as $fieldset) {
                $translationStorage->setTranslation($textdomain, $locale, $fieldset['string'], $fieldset['translation'], $overwrite=true);
            }
        }
        $translationStorage->persistFlushCache();

        return $this->redirect()->toRoute('lang_translation_index_route', array(
            'controller' => 'lang_translate_controller', 
            'action'     => 'index', 
            'textdomain' => null
        ), true);
    }
}

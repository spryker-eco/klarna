<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna\Form;

use Generated\Shared\Transfer\KlarnaPaymentTransfer;
use Spryker\Shared\Config\Config;
use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormProviderNameInterface;
use SprykerEco\Shared\Klarna\KlarnaConstants;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class InvoiceSubForm
 *
 * @package SprykerEco\Yves\Klarna\Form
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class InvoiceSubForm extends AbstractSubFormType implements SubFormInterface, SubFormProviderNameInterface
{

    const FIELD_DATE_OF_BIRTH = 'date_of_birth';
    const FIELD_TERMS = 'terms';

    const NON_PNO_COUNTRIES = [KlarnaConstants::COUNTRY_GERMANY, KlarnaConstants::COUNTRY_NETHERLAND, KlarnaConstants::COUNTRY_AUSTRIA];
    const NON_TERMS_COUNTRIES = [
        KlarnaConstants::COUNTRY_NETHERLAND,
        KlarnaConstants::COUNTRY_NORWAY,
        KlarnaConstants::COUNTRY_SWEDEN,
        KlarnaConstants::COUNTRY_FINLAND,
        KlarnaConstants::COUNTRY_DENMARK,
    ];

    /**
     * @var string
     */
    protected $countryIso2;

    /**
     * @param string $countryIso2
     */
    public function __construct($countryIso2)
    {
        $this->countryIso2 = $countryIso2;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $nonPnoCountries = $this->getNonPnoCountries();
        $nonTermsCountries = $this->getNonTermsCountries();
        if (!in_array($this->countryIso2, $nonPnoCountries)) {
            $this->addPNO($builder);
        } else {
            $this->addDateOfBirth($builder);
        }
        if (!in_array($this->countryIso2, $nonTermsCountries)) {
            $this->addTerms($builder);
        }
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \SprykerEco\Yves\Klarna\Form\InvoiceSubForm
     */
    public function addPNO(FormBuilderInterface $builder)
    {
        $builder->add(
            KlarnaConstants::FIELD_PNO,
            'text',
            [
                'label' => 'customer.PNO',
                'required' => true,
                'attr' => [
                    'placeholder' => 'customer.PNO',
                ],
            ]
        );

        return $this;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \SprykerEco\Yves\Klarna\Form\InvoiceSubForm
     */
    public function addTerms(FormBuilderInterface $builder)
    {
        $builder->add(
            self::FIELD_TERMS,
            'checkbox',
            [
                'label' => ' ',
                'mapped' => false,
            ]
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \SprykerEco\Yves\Klarna\Form\InvoiceSubForm
     */
    protected function addDateOfBirth(FormBuilderInterface $builder)
    {
        $builder->add(
            self::FIELD_DATE_OF_BIRTH,
            'birthday',
            [
                'label' => 'customer.birth_date',
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
                'input' => 'string',
                'attr' => [
                    'placeholder' => 'customer.birth_date',
                ],
            ]
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $eid = Config::getInstance()->get(KlarnaConstants::EID);

        $resolver->setDefaults(
            [
                'data_class' => KlarnaPaymentTransfer::class,
                SubFormInterface::OPTIONS_FIELD_NAME => [],
                'attr' => [
                    'EID' => $eid,
                ],
            ]
        );
    }

    /**
     * @return array
     */
    public function populateFormFields()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return strtolower(KlarnaConstants::BRAND_INVOICE);
    }

    /**
     * @return string
     */
    public function getPropertyPath()
    {
        return strtolower(KlarnaConstants::BRAND_INVOICE);
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return KlarnaConstants::PROVIDER_NAME;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return mixed
     */
    protected function getTemplatePath()
    {
        $templatePath = KlarnaConstants::PROVIDER_NAME .
            '/' . KlarnaConstants::PAYMENT_METHOD_INVOICE_TEMPLATE .
            '_' . $this->countryIso2;

        return $templatePath;
    }

    /**
     * @return array
     */
    protected function getNonPnoCountries()
    {
        $countries = [];
        foreach (self::NON_PNO_COUNTRIES as $countryConst) {
            $countries[] = Config::getInstance()->get($countryConst);
        }

        return $countries;
    }

    /**
     * @return array
     */
    protected function getNonTermsCountries()
    {
        $countries = [];
        foreach (self::NON_TERMS_COUNTRIES as $countryConst) {
            $countries[] = Config::getInstance()->get($countryConst);
        }

        return $countries;
    }

}

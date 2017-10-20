<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna\Form;

use Generated\Shared\Transfer\KlarnaPaymentTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Spryker\Shared\Config\Config;
use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormProviderNameInterface;
use SprykerEco\Shared\Klarna\KlarnaConstants;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class RateSubForm
 *
 * @package SprykerEco\Yves\Klarna\Form
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class InstallmentSubForm extends AbstractSubFormType implements SubFormInterface, SubFormProviderNameInterface
{
    const PAYMENT_METHOD = 'installment';
    const PAYMENT_CHOICES = 'paymentChoices';
    const FIELD_INSTALLMENT_INDEX = 'installment_pay_index';
    const FIELD_TERMS = 'installment_terms';
    const FIELD_DATE_OF_BIRTH = 'installment_date_of_birth';

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
     * @var \Generated\Shared\Transfer\QuoteTransfer
     */
    protected $quoteTransfer;

    /**
     * @var \SprykerEco\Yves\Klarna\Form\DataProvider\InstallmentDataProvider
     */
    protected $subFormDataProvider;

    /**
     * @param string $countryIso2
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \SprykerEco\Yves\Klarna\Form\DataProvider\InstallmentDataProvider $subFormDataProvider
     */
    public function __construct($countryIso2, $quoteTransfer, $subFormDataProvider)
    {
        $this->countryIso2 = $countryIso2;
        $this->quoteTransfer = $quoteTransfer;
        $this->subFormDataProvider = $subFormDataProvider;
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $nonPnoCountries = $this->getNonPnoCountries();
        $nonTermsCountries = $this->getNonTermsCountries();

        $this->addInstallmentDetails($builder, $options);

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
     * @return $this
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
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return \SprykerEco\Yves\Klarna\Form\InstallmentSubForm
     */
    public function addInstallmentDetails(FormBuilderInterface $builder, array $options)
    {
        $choiceKeys = array_keys($options['select_options'][static::PAYMENT_CHOICES]);
        $attr = [];
        if (count($choiceKeys) == 1) {
            $attr['style'] = 'display:none;';
        }

        $builder->add(
            static::FIELD_INSTALLMENT_INDEX,
            'choice',
            [
                'choices' => $options['select_options'][static::PAYMENT_CHOICES],
                'label' => false,
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'empty_value' => false,
                'data' => count($choiceKeys)?$choiceKeys[0]:null,
                'attr' => $attr,
            ]
        );

        return $this;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \SprykerEco\Yves\Klarna\Form\InstallmentSubForm
     */
    public function addTerms(FormBuilderInterface $builder)
    {
        $builder->add(
            static::FIELD_TERMS,
            'checkbox',
            [
                'label' => ' ',
                'mapped' => false,
                'required' => true,
            ]
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \SprykerEco\Yves\Klarna\Form\InstallmentSubForm
     */
    protected function addDateOfBirth(FormBuilderInterface $builder)
    {
        $builder->add(
            static::FIELD_DATE_OF_BIRTH,
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
        return PaymentTransfer::KLARNA_INSTALLMENT;
    }

    /**
     * @return string
     */
    public function getPropertyPath()
    {
        return PaymentTransfer::KLARNA_INSTALLMENT;
    }

    /**
     * @return string
     */
    protected function getTemplatePath()
    {
        $templatePath = KlarnaConstants::PROVIDER_NAME .
            '/' . static::PAYMENT_METHOD .
            '_' . $this->countryIso2;

        return $templatePath;
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

        $resolver->setDefaults([
            'data_class' => KlarnaPaymentTransfer::class,
            'attr' => [
                'EID' => $eid,
                'currency' => $this->subFormDataProvider->getCurrency(),
                'grandTotal' => $this->quoteTransfer->getTotals()->getGrandTotal() / 100,
            ],
        ])->setRequired([SubFormInterface::OPTIONS_FIELD_NAME]);
    }

    /**
     * @param \Symfony\Component\Form\FormView $view The view
     * @param \Symfony\Component\Form\FormInterface $form The form
     * @param array $options The options
     *
     * @return void
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $installmentResponseTransfer = $this->subFormDataProvider->getInstallmentPaymentTransfer($this->quoteTransfer);

        $view->vars['installmentResponseTransfer'] = $installmentResponseTransfer;
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return KlarnaConstants::PROVIDER_NAME;
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

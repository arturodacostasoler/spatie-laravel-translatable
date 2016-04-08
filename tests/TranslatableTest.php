<?php

namespace Spatie\Translatable\Test;

use Spatie\Translatable\Exceptions\Untranslatable;

class TranslatableTest extends TestCase
{
    /** @var TestModel */
    protected $testModel;

    public function setUp()
    {
        parent::setUp();

        $this->testModel = new TestModel();
    }

    /** @test */
    public function it_can_save_a_translated_attribute()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->name);
    }

    /** @test */
    public function it_can_set_translated_values_when_creating_a_model()
    {
        $model = TestModel::create([
           'name' => ['en' => 'testValue_en'],
        ]);

        $this->assertSame('testValue_en', $model->name);
    }

    /** @test */
    public function it_can_save_multiple_translations()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->name);
        $this->assertSame('testValue_fr', $this->testModel->getTranslation('name', 'fr'));
    }

    /** @test */
    public function it_will_return_the_value_of_the_current_locale_when_using_the_property()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue');
        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
        $this->testModel->save();

        app()->setLocale('fr');

        $this->assertSame('testValue_fr', $this->testModel->name);
    }

    /** @test */
    public function it_will_return_an_empty_string_when_getting_an_unknown_locale()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('', $this->testModel->getTranslation('name', 'de'));
    }

    /** @test */
    public function it_will_return_a_default_locale_when_a_translation_is_not_set()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->getTranslation('name', 'en', 'default'));
        $this->assertSame('default', $this->testModel->getTranslation('name', 'de', 'default'));
    }

    /** @test */
    public function it_can_get_all_translations_in_one_go()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
        $this->testModel->save();

        $this->assertSame([
            'en' => 'testValue_en',
            'fr' => 'testValue_fr',
        ], $this->testModel->getTranslations('name'));
    }

    /** @test */
    public function it_can_get_the_locales_which_have_a_translation()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
        $this->testModel->save();

        $this->assertSame(['en', 'fr'], $this->testModel->getTranslatedLocales('name'));
    }

    /** @test */
    public function it_can_forget_a_translation()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
        $this->testModel->save();

        $this->assertSame([
            'en' => 'testValue_en',
            'fr' => 'testValue_fr',
        ], $this->testModel->getTranslations('name'));

        $this->testModel->forgetTranslation('name', 'en');

        $this->assertSame([
            'fr' => 'testValue_fr',
        ], $this->testModel->getTranslations('name'));
    }

    /** @test */
    public function it_will_throw_an_exception_when_trying_to_translate_an_untranslatable_attribute()
    {
        $this->expectException(Untranslatable::class);

        $this->testModel->setTranslation('untranslated', 'en', 'value');
    }

    /** @test */
    public function it_is_compatible_with_accessors_that_have_the_same_name_as_a_translatable_attribute()
    {
        $testModel = new class extends TestModel
 {
     public function getOtherFieldAttribute() : string
     {
         return 'accessorName';
     }
 };

        $this->assertEquals((new $testModel())->otherField, 'accessorName');
    }
}
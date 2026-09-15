import * as React from 'react';
import { router } from '@inertiajs/react';
import { IPropsBase } from '@/script/System/System';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import {
  E_PAGES,
  getPagesHref,
  getPagesName,
} from '@/script/Enum/Server/App/EPages';
import {
  FormInputForm,
  IFormInputForm,
} from '@/script/Pages/Sample/InputForm/FormInputForm';
import {
  parseServerValidation,
  validateAll,
} from '@/script/Pages/Sample/InputForm/FormInputFormValidation';
import { E_STATUS_CODE } from '@/script/Enum/Server/App/EStatusCode';
import { useIndexContext } from '@/script/Pages/Sample/InputForm/Index';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { useMutationSampleInputFormUpdate } from '@/script/Hooks/Sample/Mutations/useMutationSampleInputFormUpdate';
import { Page } from '@/script/Pages/Page';

interface IProps extends IPropsBase {}
export const InputForm: React.FC<IProps> = () => {
  const { onStart, onFinish } = useProgressContext();
  const { validation, setValidation } = useIndexContext();
  const [formInputForm, setFormInputForm] = React.useState<IFormInputForm>({
    inputSingleNumber: '',
    inputSingleNotZero: '',
    inputSingleRange: '',
    inputSingleEmail: '',
    inputMulti: '',
    inputSelectStringId: 0,
  });
  const [processingUpdate, mutationUpdate] = useMutationSampleInputFormUpdate();
  const loading = processingUpdate;

  /**
   * 値の更新.
   */
  const handleUpdate = React.useCallback(
    (newValues: Partial<IFormInputForm>) => {
      setFormInputForm(prevState => ({ ...prevState, ...newValues }));
    },
    [setFormInputForm],
  );

  /**
   * graphqlで送信.
   */
  const handleSaveGraphQL = () => {
    // バリデーション実行.
    const validation = validateAll(formInputForm);
    setValidation(validation);

    // エラーがある場合は処理をしない.
    if (validation.hasError) {
      return;
    }

    // 更新処理の実施.
    mutationUpdate(
      {
        ...formInputForm,
      },
      [],
    ).then(res => {
      switch (res.statusCode) {
        case E_STATUS_CODE.UNPROCESSABLE_ENTITY:
          setValidation(parseServerValidation(res));
          break;
        default:
          break;
      }
    });
  };

  const handleSavePost = () => {
    // バリデーション実行.
    const validation = validateAll(formInputForm);
    setValidation(validation);

    // エラーがある場合は処理をしない.
    if (validation.hasError) {
      return;
    }

    // POST通信でコントローラーを呼び出す.
    router.visit(getPagesHref(E_PAGES.SAMPLE_INPUT_FORM__CREATE), {
      method: 'post',
      onStart,
      onFinish,
      data: {
        ...formInputForm,
      },
    });
  };

  return (
    <Page>
      <TypoH1>{getPagesName(E_PAGES.SAMPLE_INPUT_FORM)}</TypoH1>
      <FormInputForm
        sx={{
          width: '100%',
        }}
        loading={loading}
        formInputForm={formInputForm}
        validation={validation}
        handleUpdate={handleUpdate}
        handleSaveGraphQL={handleSaveGraphQL}
        handleSavePost={handleSavePost}
      />
    </Page>
  );
};

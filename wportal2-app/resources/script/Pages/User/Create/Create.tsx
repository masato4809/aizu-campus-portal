import * as React from 'react';
import { Paper } from '@mui/material';
import { router } from '@inertiajs/react';
import { IPropsBase } from '@/script/System/System';
import { BackLink } from '@/script/Pages/Common/BackLink';
import {
  E_PAGES,
  getPagesHref,
  getPagesName,
} from '@/script/Enum/Server/App/EPages';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { Page } from '@/script/Pages/Page';
import { FormCreate, IFormCreate } from '@/script/Pages/User/Create/FormCreate';
import { validateAll } from '@/script/Pages/User/Create/FormCreateValidation';
import { useIndexContext } from '@/script/Pages/User/Create/Index';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const Create: React.FC<IProps> = () => {
  const { onStart, onFinish } = useProgressContext();
  const { validation, setValidation } = useIndexContext();
  const [formCreate, setFormCreate] = React.useState<IFormCreate>({
    name: '',
    nickname: '',
    email: '',
  });

  /**
   * 値の更新.
   */
  const handleUpdate = React.useCallback(
    (newValues: Partial<IFormCreate>) => {
      setFormCreate(prevState => ({ ...prevState, ...newValues }));
    },
    [setFormCreate],
  );

  /**
   * 値の送信.
   */
  const handleSubmit = () => {
    // バリデーション実施.
    const validation = validateAll(formCreate);
    setValidation(validation);

    // エラーがある場合は処理をしない.
    if (validation.hasError) {
      return;
    }

    // 更新処理の実施.
    router.visit(getPagesHref(E_PAGES.USER__CREATE), {
      method: 'post',
      preserveState: true,
      preserveScroll: true,
      data: {
        ...formCreate,
      },
      onStart,
      onFinish,
    });
  };

  return (
    <Page>
      <BackLink current={E_PAGES.USER__NEW} />
      <TypoH1>{getPagesName(E_PAGES.USER__NEW)}</TypoH1>
      <Paper
        sx={{
          marginTop: responsiveSpacing(4),
          padding: responsiveSpacing(4),
        }}
      >
        <FormCreate
          formCreate={formCreate}
          validation={validation}
          handleUpdate={handleUpdate}
          handleSubmit={handleSubmit}
        />
      </Paper>
    </Page>
  );
};

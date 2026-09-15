import * as React from 'react';
import { Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import {
  E_PAGES,
  getPagesHref,
  getPagesName,
} from '@/script/Enum/Server/App/EPages';
import { Page } from '@/script/Pages/Page';
import { BackLink } from '@/script/Pages/Common/BackLink';
import { responsiveSpacing } from '@/script/System/Responsive';
import {
  defaultInterface,
  FormCreate,
  IFormCreate,
} from '@/script/Pages/GoodJob/New/FormCreate';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { router } from '@inertiajs/react';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { useIndexContext } from '@/script/Pages/GoodJob/New/Index';
import { validateAll } from '@/script/Pages/GoodJob/New/FormCreateValidation';

interface IProps extends IPropsBase {}
export const New: React.FC<IProps> = () => {
  const { onStart, onFinish } = useProgressContext();
  const { validation, setValidation } = useIndexContext();
  const { authUser } = useCommonIndexContext();
  const [formCreate, setFormCreate] = React.useState<IFormCreate>({
    ...defaultInterface,
    fromTrnUserId: authUser.trnUser?.id || 0,
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
    // バリデーション関連.
    const validation = validateAll(formCreate);
    setValidation(validation);

    // エラーがある場合は処理をしない.
    if (validation.hasError) {
      return;
    }

    router.visit(getPagesHref(E_PAGES.GOOD_JOB__CREATE), {
      method: 'post',
      data: {
        ...formCreate,
      },
      onStart,
      onFinish,
    });
  };

  return (
    <Page>
      <BackLink current={E_PAGES.GOOD_JOB__NEW} />
      <TypoH1>{getPagesName(E_PAGES.GOOD_JOB__NEW)}</TypoH1>
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

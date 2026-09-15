import * as React from 'react';
import { Paper } from '@mui/material';
import { router } from '@inertiajs/react';
import { IPropsBase } from '@/script/System/System';
import { useIndexContext } from '@/script/Pages/Division/New/Index';
import {
  FormCreate,
  IFormCreate,
} from '@/script/Pages/Division/New/FormCreate';
import { validateAll } from '@/script/Pages/Division/New/FormCreateValidation';
import {
  E_PAGES,
  getPagesHref,
  getPagesName,
} from '@/script/Enum/Server/App/EPages';
import { BackLink } from '@/script/Pages/Common/BackLink';
import { Page } from '@/script/Pages/Page';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { useProgressContext } from '@/script/Provider/ProgressProvider';

import { responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const New: React.FC<IProps> = () => {
  const { onStart, onFinish } = useProgressContext();
  const { validation, setValidation } = useIndexContext();
  const [formCreate, setFormCreate] = React.useState<IFormCreate>({
    name: '',
    explain: '',
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
    const validation = validateAll(formCreate);
    setValidation(validation);
    if (validation.hasError) {
      return;
    }

    router.visit(getPagesHref(E_PAGES.DIVISION__CREATE), {
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
      <BackLink current={E_PAGES.DIVISION__NEW} />
      <TypoH1>{getPagesName(E_PAGES.DIVISION__NEW)}</TypoH1>
      <Paper
        sx={{
          marginTop: responsiveSpacing(4),
          padding: responsiveSpacing(4),
        }}
      >
        <FormCreate
          formCreate={formCreate}
          handleSubmit={handleSubmit}
          handleUpdate={handleUpdate}
          validation={validation}
        />
      </Paper>
    </Page>
  );
};

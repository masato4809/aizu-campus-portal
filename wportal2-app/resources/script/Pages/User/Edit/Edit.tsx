import * as React from 'react';
import { Box, Paper } from '@mui/material';
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
import { useIndexContext } from '@/script/Pages/User/Edit/Index';
import { FormEdit, IFormEdit } from '@/script/Pages/User/Edit/FormEdit';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { E_BUTTON_TYPE } from '@/script/Component/Button/EButtonType';
import { Closable } from '@/script/Component/Misc/Closable';
import { DialogYesNo } from '@/script/Component/Misc/DialogYesNo';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const Edit: React.FC<IProps> = () => {
  const { trnUser } = useIndexContext();
  const { onStart, onFinish } = useProgressContext();
  const [formEdit, setFormEdit] = React.useState<IFormEdit>({
    enableLegacyLogin: false,
    overwritePassword: '',
  });
  const [openConfirm, setOpenConfirm] = React.useState<boolean>(false);

  /**
   * 値の更新.
   */
  const handleUpdate = React.useCallback(
    (newValues: Partial<IFormEdit>) => {
      setFormEdit(prevState => ({ ...prevState, ...newValues }));
    },
    [setFormEdit],
  );

  /**
   * 値の送信.
   */
  const handleSubmit = () => {
    // TODO: 必用があればバリデーション.

    // 更新処理の実施.
    router.visit(getPagesHref(E_PAGES.USER__UPDATE), {
      method: 'post',
      preserveState: true,
      preserveScroll: true,
      data: {
        authId: trnUser.authId,
        trnUserId: trnUser.id,
        enableLegacyLogin: formEdit.enableLegacyLogin,
        overwritePassword: formEdit.overwritePassword,
      },
      onStart,
      onFinish,
    });
  };

  /**
   * メンバーの削除.
   */
  const handleDelete = () => {
    router.visit(getPagesHref(E_PAGES.USER__DELETE), {
      method: 'post',
      preserveState: true,
      preserveScroll: true,
      data: {
        trnUserId: trnUser.id,
      },
      onStart,
      onFinish,
    });
  };

  return (
    <Page>
      <Closable open={openConfirm}>
        <DialogYesNo
          open={openConfirm}
          labels={{
            content: 'メンバーを削除しますか？',
          }}
          actions={{
            submit: handleDelete,
            close: () => setOpenConfirm(false),
          }}
        />
      </Closable>
      <BackLink
        current={E_PAGES.USER__EDIT}
        replace={[
          {
            target: '$userId',
            value: String(trnUser.id),
          },
        ]}
      />
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'space-between',
        }}
      >
        <TypoH1>{getPagesName(E_PAGES.USER__EDIT)}</TypoH1>
        <ButtonGeneral
          sx={{
            width: responsiveSize(200),
            color: 'white',
          }}
          buttonType={E_BUTTON_TYPE.CONTAINED_SECONDARY}
          label="メンバーを削除"
          onClick={() => setOpenConfirm(true)}
        />
      </Box>
      <Paper
        sx={{
          marginTop: responsiveSpacing(4),
          padding: responsiveSpacing(4),
        }}
      >
        <FormEdit
          sx={{
            width: '100%',
          }}
          formEdit={formEdit}
          handleUpdate={handleUpdate}
          handleSubmit={handleSubmit}
        />
      </Paper>
    </Page>
  );
};

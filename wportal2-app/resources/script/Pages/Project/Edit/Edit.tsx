import * as React from 'react';
import { router } from '@inertiajs/react';
import { Box, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { BackLink } from '@/script/Pages/Common/BackLink';
import {
  E_PAGES,
  getPagesHref,
  getPagesName,
} from '@/script/Enum/Server/App/EPages';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { useIndexContext } from '@/script/Pages/Project/Edit/Index';
import { Page } from '@/script/Pages/Page';
import { FormEdit, IFormEdit } from '@/script/Pages/Project/Edit/FormEdit';
import { validateAll } from '@/script/Pages/Project/Edit/FormEditValidation';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { DialogYesNo } from '@/script/Component/Misc/DialogYesNo';
import { Closable } from '@/script/Component/Misc/Closable';
import { E_BUTTON_TYPE } from '@/script/Component/Button/EButtonType';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}

export const Edit: React.FC<IProps> = () => {
  const { onStart, onFinish } = useProgressContext();
  const { trnProjectList, validation, setValidation } = useIndexContext();
  const trnProject = trnProjectList.first();
  const [formEdit, setFormEdit] = React.useState<IFormEdit>({
    id: trnProject.id,
    name: trnProject.name,
    explain: trnProject.explain,
    editNotification: false,
    notificationList:
      trnProject.trnProjectNotification?.map(notification => {
        return {
          id: notification.id,
          trnProjectId: notification.trnProjectId,
          notificationType: notification.notificationType,
          notificationValue: notification.notificationValue,
        };
      }) ?? [],
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
    // バリデーション実施.
    const validation = validateAll(formEdit);
    setValidation(validation);

    // エラーがある場合は処理をしない.
    if (validation.hasError) {
      return;
    }

    // 更新処理の実施.
    router.visit(getPagesHref(E_PAGES.PROJECT__UPDATE), {
      method: 'post',
      preserveState: true,
      preserveScroll: true,
      data: {
        trnProjectId: formEdit.id,
        name: formEdit.name,
        explain: formEdit.explain,
        editNotification: formEdit.editNotification,
        notificationListJson: JSON.stringify(formEdit.notificationList),
      },
      onStart,
      onFinish,
    });
  };

  // プロジェクト削除用関数
  const handleDelete = () => {
    // 更新の実施.
    router.visit(getPagesHref(E_PAGES.PROJECT__DELETE), {
      method: 'post',
      data: {
        trnProjectId: String(trnProject.id),
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
            content: '現在開いているプロジェクトを削除しますか？',
          }}
          actions={{
            submit: handleDelete,
            close: () => setOpenConfirm(false),
          }}
        />
      </Closable>
      <BackLink
        current={E_PAGES.PROJECT__EDIT}
        replace={[
          {
            target: '$projectId',
            value: String(trnProject.id),
          },
        ]}
      />

      <Box
        sx={{
          display: 'flex',
          justifyContent: 'space-between',
        }}
      >
        <TypoH1>
          {getPagesName(E_PAGES.PROJECT__EDIT)}[{trnProject.id}]
        </TypoH1>
        <ButtonGeneral
          sx={{
            width: responsiveSize(200),
            color: 'white',
          }}
          buttonType={E_BUTTON_TYPE.CONTAINED_SECONDARY}
          label="プロジェクトを削除"
          onClick={() => setOpenConfirm(true)}
        />
      </Box>
      <Paper
        sx={{
          marginTop: responsiveSpacing(4),
          padding: responsiveSpacing(4),
          display: 'flex',
          flexDirection: 'column',
          gap: responsiveSpacing(2),
        }}
      >
        <FormEdit
          formEdit={formEdit}
          validation={validation}
          handleUpdate={handleUpdate}
        />
        <ButtonGeneral
          sx={{
            width: responsiveSize(200),
          }}
          label="更新"
          onClick={handleSubmit}
        />
      </Paper>
    </Page>
  );
};

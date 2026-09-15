import * as React from 'react';
import { Box, Divider, Paper } from '@mui/material';
import { router } from '@inertiajs/react';
import { IPropsBase } from '@/script/System/System';
import { Page } from '@/script/Pages/Page';
import {
  E_PAGES,
  getPagesHref,
  getPagesName,
} from '@/script/Enum/Server/App/EPages';
import { E_BUTTON_TYPE } from '@/script/Component/Button/EButtonType';
import { BackLink } from '@/script/Pages/Common/BackLink';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { useIndexContext } from '@/script/Pages/Division/Edit/Index';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { Avatar } from '@/script/Pages/Common/Avatar';
import { FormEdit, IFormEdit } from '@/script/Pages/Division/Edit/FormEdit';
import { validateAll } from '@/script/Pages/Division/Edit/FormEditValidation';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { DialogYesNo } from '@/script/Component/Misc/DialogYesNo';
import { Closable } from '@/script/Component/Misc/Closable';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const Edit: React.FC<IProps> = () => {
  const { onStart, onFinish } = useProgressContext();
  const { trnDivisionList, validation, setValidation } = useIndexContext();
  const trnDivision = trnDivisionList.first();
  const [formEdit, setFormEdit] = React.useState<IFormEdit>({
    id: trnDivision.id,
    name: trnDivision.name,
    explain: trnDivision.explain,
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
    router.visit(getPagesHref(E_PAGES.DIVISION__UPDATE), {
      method: 'post',
      preserveState: true,
      preserveScroll: true,
      data: {
        trnDivisionId: formEdit.id,
        name: formEdit.name,
        explain: formEdit.explain,
      },
      onStart,
      onFinish,
    });
  };

  // 課の削除
  const handleDelete = () => {
    router.visit(getPagesHref(E_PAGES.DIVISION__DELETE), {
      method: 'post',
      data: {
        trnDivisionId: String(trnDivision.id),
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
            content: 'この課を削除しますか？',
          }}
          actions={{
            submit: handleDelete,
            close: () => setOpenConfirm(false),
          }}
        />
      </Closable>
      <BackLink
        current={E_PAGES.DIVISION__EDIT}
        replace={[
          {
            target: '$divisionId',
            value: String(trnDivision.id),
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
          {getPagesName(E_PAGES.DIVISION__EDIT)}[{trnDivision.id}]
        </TypoH1>
        <ButtonGeneral
          sx={{
            width: responsiveSize(150),
            color: 'white',
          }}
          buttonType={E_BUTTON_TYPE.CONTAINED_SECONDARY}
          label="課を削除"
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
        <Box>
          <Box sx={{ display: 'flex' }}>
            <Box>
              <TypoText
                sx={{
                  minWidth: responsiveSize(200),
                  width: responsiveSize(200),
                }}
              >
                メンバー
              </TypoText>
            </Box>
            <Box
              sx={{
                marginTop: responsiveSpacing(2),
                display: 'flex',
                flexWrap: 'wrap',
                gap: responsiveSpacing(2),
              }}
            >
              {trnDivision.trnDivisionUser?.map(trnDivisionUser => {
                return (
                  <Avatar
                    key={trnDivisionUser.id}
                    trnUser={trnDivisionUser.trnUser}
                  />
                );
              })}
            </Box>
          </Box>
          <Divider sx={{ marginTop: responsiveSize(6) }} />
        </Box>
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

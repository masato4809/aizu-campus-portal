import * as React from 'react';
import { router } from '@inertiajs/react';
import { Box, Divider, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { Page } from '@/script/Pages/Page';
import {
  E_PAGES,
  getPagesHref,
  getPagesName,
} from '@/script/Enum/Server/App/EPages';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { BackLink } from '@/script/Pages/Common/BackLink';
import { useIndexContext } from '@/script/Pages/Division/Show/Index';
import { LabelValue } from '@/script/Pages/Common/LabelValue';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { Avatar } from '@/script/Pages/Common/Avatar';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { InertiaLink } from '@/script/Component/Misc/InertiaLink';
import { DialogReplaceTrnUser } from '@/script/Pages/Common/Dialog/DialogReplaceTrnUser';
import { Closable } from '@/script/Component/Misc/Closable';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const Show: React.FC<IProps> = () => {
  const { onStart, onFinish } = useProgressContext();
  const { trnDivisionList } = useIndexContext();
  const trnDivision = trnDivisionList.first();
  const [openReplace, setOpenReplace] = React.useState<boolean>(false);

  /**
   * メンバー入替を選択.
   */
  const handleClickReplace = () => {
    setOpenReplace(true);
  };

  /**
   * メンバー入替の送信.
   */
  const handleSubmitReplace = (newValues: number[]) => {
    // onStart();
    setOpenReplace(false);

    // 更新処理の実施.
    router.visit(getPagesHref(E_PAGES.DIVISION__UPDATE_USER), {
      method: 'post',
      data: {
        trnDivisionId: trnDivision.id,
        trnUserIdList: newValues,
      },
      onStart,
      onFinish,
    });
  };

  return (
    <Page>
      <Closable open={openReplace}>
        <DialogReplaceTrnUser
          open={openReplace}
          defaultUserIdList={
            trnDivision.trnDivisionUser?.map(v => v.trnUserId) ?? []
          }
          handleSubmit={handleSubmitReplace}
          handleClose={() => setOpenReplace(false)}
        />
      </Closable>
      <BackLink current={E_PAGES.DIVISION__SHOW} />
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'space-between',
        }}
      >
        <TypoH1>
          {getPagesName(E_PAGES.DIVISION__SHOW)}[{trnDivision.id}]
        </TypoH1>
        <InertiaLink
          href={getPagesHref(E_PAGES.DIVISION__EDIT, [
            {
              target: '$divisionId',
              value: String(trnDivision.id),
            },
          ])}
        >
          <ButtonGeneral
            sx={{
              minWidth: responsiveSize(140),
            }}
            label="編集"
          />
        </InertiaLink>
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
        <LabelValue label="id" value={String(trnDivision.id)} />
        <LabelValue label="名前" value={trnDivision.name} />
        <LabelValue
          valueSx={{
            whiteSpace: 'pre-wrap',
          }}
          label="説明"
          value={trnDivision.explain}
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
              <ButtonGeneral
                sx={{
                  marginTop: responsiveSpacing(2),
                }}
                label="入替"
                onClick={handleClickReplace}
              />
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
      </Paper>
    </Page>
  );
};

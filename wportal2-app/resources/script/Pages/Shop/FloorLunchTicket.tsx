import * as React from 'react';
import { Box, Grid } from '@mui/material';
import { router } from '@inertiajs/react';
import { IPropsBase } from '@/script/System/System';
import { responsiveSpacing } from '@/script/System/Responsive';
import { LunchTicket } from '@/script/Pages/Shop/LunchTicket';
import { useIndexContext } from '@/script/Pages/Shop/Index';
import { E_COLOR } from '@/script/Enum/EColor';
import { TypoText2 } from '@/script/Component/Typography/TypoText2';
import { IAppMstGoods } from '@/script/Models/App/Mst/MstGoodsList';
import { DialogCalendarUserScheduleSelect } from '@/script/Pages/Shop/DialogCalendarUseScheduleSelect/DialogCalendarUserScheduleSelect';
import { Closable } from '@/script/Component/Misc/Closable';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { DialogConfirm } from '@/script/Component/Misc/DialogConfirm';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { useProgressContext } from '@/script/Provider/ProgressProvider';

interface IProps extends IPropsBase {}
export const FloorLunchTicket: React.FC<IProps> = () => {
  const { authUser } = useCommonIndexContext();
  const { mstGoodsList } = useIndexContext();
  const { onStart, onFinish } = useProgressContext();
  const [openCalendarDialog, setOpenCalendarDialog] = React.useState<
    IAppMstGoods | undefined
  >(undefined);
  const [openNoticeDialog, setOpenNoticeDialog] =
    React.useState<boolean>(false);
  const [openConfirmDialog, setOpenConfirmDialog] =
    React.useState<boolean>(false);

  /**
   * チケットを選択.
   */
  const handleClick = (mstGoods: IAppMstGoods) => {
    // 所持金が足りない場合は警告ダイアログ.
    const current = authUser.trnUser?.currentGold ?? 0;
    if (current < mstGoods.price) {
      setOpenNoticeDialog(true);
      return;
    }

    setOpenCalendarDialog(mstGoods);
  };

  /**
   * 時間を選択.
   */
  const handleSubmit = (target: string) => {
    /**
     * サーバーに送信.
     */
    router.post(
      getPagesHref(E_PAGES.SHOP__LUNCH_TICKET),
      {
        mstGoodsId: openCalendarDialog?.id ?? 0,
        target,
      },
      {
        onStart,
        onFinish: () => {
          setOpenCalendarDialog(undefined);
          setOpenConfirmDialog(true);
          onFinish();
        },
        preserveState: true,
        preserveScroll: true,
      },
    );
  };

  return (
    <Box
      sx={{
        padding: responsiveSpacing(4),
      }}
    >
      <Closable open={!!openCalendarDialog}>
        <DialogCalendarUserScheduleSelect
          open={!!openCalendarDialog}
          title={`[${openCalendarDialog?.name}(${openCalendarDialog?.price})]のスケジュールを選択してください`}
          comment={openCalendarDialog?.comment ?? ''}
          emailList={[authUser.email, openCalendarDialog?.authorEmail ?? '']}
          handleSubmit={handleSubmit}
          handleClose={() => setOpenCalendarDialog(undefined)}
        />
      </Closable>
      <Closable open={openNoticeDialog}>
        <DialogConfirm
          open={openNoticeDialog}
          labels={{
            content: '購入できません、所持金が不足しています。',
          }}
          actions={{
            confirm: () => setOpenNoticeDialog(false),
          }}
        />
      </Closable>
      <Closable open={openConfirmDialog}>
        <DialogConfirm
          open={openConfirmDialog}
          labels={{
            content: '予約が完了しました\n以降の調整は直接行ってください',
          }}
          actions={{
            confirm: () => setOpenConfirmDialog(false),
          }}
        />
      </Closable>
      <Grid
        sx={{
          display: 'grid',
          gridTemplateColumns: `repeat(auto-fill, minmax(300px, 1fr))`,
          justifyContent: 'center',
          width: '90%',
          margin: '0 auto',
          gap: '40px',
        }}
      >
        {mstGoodsList.list().map(mstGoods => {
          return (
            <Grid key={mstGoods.id} item onClick={() => handleClick(mstGoods)}>
              <Box>
                <LunchTicket mstGoods={mstGoods} />
              </Box>
              <Box
                sx={{
                  marginTop: responsiveSpacing(2),
                  width: '300px',
                }}
              >
                <TypoText2
                  sx={{
                    padding: '4px 10px',
                    background: E_COLOR.PRIMARY_LIGHT,
                  }}
                  bold
                >
                  {mstGoods.name}
                </TypoText2>
                <TypoText2
                  sx={{
                    background: E_COLOR.WHITE,
                    padding: '4px 10px',
                    whiteSpace: 'pre-wrap',
                  }}
                >
                  {mstGoods.explain}
                </TypoText2>
              </Box>
            </Grid>
          );
        })}
      </Grid>
    </Box>
  );
};

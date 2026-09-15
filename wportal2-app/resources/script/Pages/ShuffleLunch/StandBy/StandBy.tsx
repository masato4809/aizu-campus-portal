import * as React from 'react';
import { Box, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { responsiveSpacing } from '@/script/System/Responsive';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { E_COLOR } from '@/script/Enum/EColor';
import { useIndexContext } from '@/script/Pages/ShuffleLunch/Index';
import {
  E_EVENT_TIME_ZONE,
  getEventTimeZoneName,
} from '@/script/Enum/Server/App/ShuffleLunch/EEventTimeZone';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { E_REGISTER } from '@/script/Enum/Server/App/ShuffleLunch/ERegister';
import { StatusUnregistered } from '@/script/Pages/ShuffleLunch/StandBy/StatusUnregistered';
import { StatusRegistered } from '@/script/Pages/ShuffleLunch/StandBy/StatusRegistered';

interface IProps extends IPropsBase {}
export const StandBy: React.FC<IProps> = () => {
  const { trnShuffleLunchEntryList } = useIndexContext();
  const { authUser } = useCommonIndexContext();

  // 登録状況.
  const register = trnShuffleLunchEntryList
    .list()
    .some(v => v.trnUserId === authUser.trnUser?.id)
    ? E_REGISTER.REGISTERED
    : E_REGISTER.UNREGISTERED;

  /**
   * 現在の参加者状況.
   */
  const nodeEntryState = (): React.ReactNode => {
    // 表示するタイムゾーン.
    const timeZoneList = Object.values(E_EVENT_TIME_ZONE).filter(
      v => v !== E_EVENT_TIME_ZONE.INVALID,
    );

    return timeZoneList.map(zone => {
      // ゾーン毎の参加人数.
      const memberCount = trnShuffleLunchEntryList
        .list()
        .filter(v => v.eventTimeZone === zone).length;

      return (
        <React.Fragment key={zone}>
          <Box
            sx={{
              marginTop: responsiveSpacing(2),
              display: 'flex',
              gap: responsiveSpacing(2),
            }}
          >
            <TypoText>{getEventTimeZoneName(zone)}</TypoText>
            <TypoText>{`${memberCount}名`}</TypoText>
          </Box>
        </React.Fragment>
      );
    });
  };

  /**
   * 登録状況の表示.
   */
  const nodeRegisterStatus = (): React.ReactNode => {
    switch (register) {
      case E_REGISTER.REGISTERED:
        return <StatusRegistered />;
      case E_REGISTER.UNREGISTERED:
        return <StatusUnregistered />;
    }
  };

  return (
    <>
      <Paper
        sx={{
          marginTop: responsiveSpacing(4),
          padding: responsiveSpacing(4),
        }}
      >
        <Box
          sx={{
            display: 'flex',
            justifyContent: 'center',
          }}
        >
          <Box
            sx={{
              padding: responsiveSpacing(4),
              borderRadius: responsiveSpacing(4),
              display: 'flex',
              flexDirection: 'column',
              alignItems: 'center',
              background: E_COLOR.PRIMARY_LIGHT,
            }}
          >
            <TypoH2>本日はまだマッチングが実施されていません</TypoH2>
            <TypoH2 sx={{ marginTop: responsiveSpacing(4) }}>
              現在の参加者状況
            </TypoH2>
            {nodeEntryState()}
          </Box>
        </Box>
      </Paper>
      {nodeRegisterStatus()}
    </>
  );
};

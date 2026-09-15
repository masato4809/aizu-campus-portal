import * as React from 'react';
import { IPropsBase } from '@/script/System/System';
import { EEventTimeZone } from '@/script/Enum/Server/App/ShuffleLunch/EEventTimeZone';
import { useIndexContext } from '@/script/Pages/ShuffleLunch/Index';
import { Box, Paper } from '@mui/material';
import { responsiveSpacing } from '@/script/System/Responsive';
import { EventGroup } from '@/script/Pages/ShuffleLunch/Established/EventGroup';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { E_COLOR } from '@/script/Enum/EColor';

interface IProps extends IPropsBase {
  eventTimeZone: EEventTimeZone;
}
export const EventTimezoneResult: React.FC<IProps> = ({
  sx,
  eventTimeZone,
}) => {
  const { trnShuffleLunchGroupList } = useIndexContext();
  const groupIdList =
    trnShuffleLunchGroupList.getGroupIdListByEventTimeZone(eventTimeZone);

  if (groupIdList.length === 0) {
    return (
      <Paper
        sx={{
          marginTop: responsiveSpacing(4),
          padding: responsiveSpacing(4),
          display: 'flex',
          justifyContent: 'center',
          ...sx,
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
          <TypoText>本日のマッチ結果はありません</TypoText>
        </Box>
      </Paper>
    );
  }

  return (
    <Box
      sx={{
        display: 'flex',
        flexWrap: 'wrap',
        gap: responsiveSpacing(4),
        ...sx,
      }}
    >
      {groupIdList.map(groupId => {
        return (
          <EventGroup
            key={groupId}
            eventTimeZone={eventTimeZone}
            groupId={groupId}
          />
        );
      })}
    </Box>
  );
};
